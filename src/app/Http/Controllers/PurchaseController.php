<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $paymentMethods = [
            'convenience' => 'コンビニ支払い',
            'credit' => 'カード支払い',
        ];

        $user = Auth::user()->fresh();

        return view('items.purchase', compact('item', 'paymentMethods', 'user'));
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        $user = $request->user();

        if ($item->user_id === $user->id) {
            return back()->withErrors(['item' => '自分が出品した商品は購入できません。'])->withInput();
        }

        if ($item->buyer_id !== null) {
            return back()->withErrors(['item' => 'この商品はすでに購入されています。'])->withInput();
        }

        if (blank($user->postal) || blank($user->address)) {
            return back()
                ->withErrors(['shipping' => '購入前に配送先住所を登録してください。'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $item, $user) {
            $item->refresh();
            if ($item->buyer_id !== null) abort(409, 'この商品はすでに購入されています。');

            $validated = $request->validated();

            $item->forceFill([
                'buyer_id'          => $user->id,
                'payment_method'    => $validated['payment_method'],
                'shipping_postal'   => $user->postal,
                'shipping_address'  => $user->address,
                'shipping_building' => $user->building,
            ])->save();
        });
        
        return redirect()->route('items.index')->with('success', '購入が完了しました');
    }


    public function editAddress(Item $item)
    {
        $user = Auth::user();

        $redirectTo = request('redirect_to', route('purchase.show', $item));

        return view('items.address', [
            'item'       => $item,
            'user'       => $user,
            'redirectTo' => $redirectTo,
        ]);

        return view('items.address', compact('item', 'user'));
    }

    public function updateAddress(Request $request, Item $item)
    {
        $request->validate([
            'postal'      => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
            'redirect_to' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        $user->update([
            'postal' => $request->postal,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.show', ['item' => $item->id])
            ->with('success', '配送先住所を更新しました。');
    }
    public function showAddress(Item $item)
    {
        $paymentMethods = [
            'convenience' => 'コンビニ支払い',
            'credit' => 'カード支払い',
        ];

        // ここが重要！ユーザー情報をDBから最新で取得しなおす
        $user = Auth::user()->fresh();

        return view('items.purchase', compact('item', 'paymentMethods', 'user'))
            ->with('success', '配送先住所を更新しました。');
    }
}

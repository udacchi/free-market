<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $paymentMethods = [
            'convenience' => 'コンビニ支払い',
            'credit' => 'カード支払い',
        ];

        $user = auth()->user();

        return view('items.purchase', compact('item', 'paymentMethods', 'user'));
    }

    public function store(Request $request, Item $item)
    {
        $request->validate([
            'payment_method' => ['required', 'in:convenience,credit']
        ]);

        $item->buyer_id = Auth::id();
        $success = $item->save();

        return redirect()->route('items.index')->with('success', '購入が完了しました');
    }


    public function editAddress(Item $item)
    {
        $user = Auth::user();
        return view('items.address', compact('item', 'user'));
    }

    public function updateAddress(Request $request, Item $item)
    {
        $request->validate([
            'postal' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'postal' => $request->postal,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.address', ['item' => $item->id])
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

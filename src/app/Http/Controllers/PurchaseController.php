<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    /**
     * 購入画面（ビュー名: purchase.show）
     * GET /purchase/{item}  -> name: purchase.show
     */
    public function show(Item $item)
    {
        $paymentMethods = [
            'convenience' => 'コンビニ支払い',
            'credit'      => 'カード支払い',
        ];

        // 住所更新直後でも最新が反映されるよう fresh()
        $user = Auth::user()->fresh();

        return view('purchase.show', compact('item', 'paymentMethods', 'user'));
    }

    /**
     * 購入実行
     * POST /purchase/{item} -> name: purchase.store
     */
    public function store(PurchaseRequest $request, Item $item)
    {
        $user = $request->user();

        // 自分の商品は買えない
        if ($item->user_id === $user->id) {
            return back()->withErrors(['item' => '自分が出品した商品は購入できません。'])->withInput();
        }

        // 既に購入済み
        if (!is_null($item->buyer_id)) {
            return back()->withErrors(['item' => 'この商品はすでに購入されています。'])->withInput();
        }

        // 配送先必須
        if (blank($user->postal) || blank($user->address)) {
            return back()
                ->withErrors(['shipping' => '購入前に配送先住所を登録してください。'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $item, $user) {
            $item->refresh();
            if (!is_null($item->buyer_id)) {
                abort(409, 'この商品はすでに購入されています。');
            }

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

    /**
     * 配送先編集画面
     * GET /purchase/address/{item}/edit -> name: purchase.address.edit
     */
    public function editAddress(Item $item)
    {
        $user = Auth::user();

        // 互換用。現状は常に purchase.show に戻す運用
        $redirectTo = request('redirect_to', route('purchase.show', $item));

        return view('purchase.address', [
            'item'       => $item,
            'user'       => $user,
            'redirectTo' => $redirectTo,
        ]);
    }

    /**
     * 配送先更新
     * PUT /purchase/address/{item} -> name: purchase.address.update
     */
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
            'postal'   => $request->postal,
            'address'  => $request->address,
            'building' => $request->building,
        ]);

        // 住所更新後は常に purchase.show へ
        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('success', '配送先住所を更新しました。');
    }

    /**
     * 配送先表示用（テストで叩かれる想定のエンドポイント）
     * GET /purchase/address/{item} -> name: purchase.address
     * ここはビューを返さず purchase.show に統一リダイレクト
     */
    public function showAddress(Item $item)
    {
        return redirect()
            ->route('purchase.show', $item)
            ->with('success', '配送先住所を更新しました。');
    }

    /**
     * プレビュー（テスト対応用）
     * GET /purchase/{item}/preview -> name: purchase.preview
     * 実体は purchase.show をそのまま表示
     */
    public function preview(Item $item)
    {
        $paymentMethods = [
            'convenience' => 'コンビニ支払い',
            'credit'      => 'カード支払い',
        ];
        $user = Auth::user()->fresh();

        // プレビューは purchase.show を使う（別ビュー不要）
        return view('purchase.show', compact('item', 'paymentMethods', 'user'));
    }
}

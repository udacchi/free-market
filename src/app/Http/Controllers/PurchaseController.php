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
            'payment_method' => ['required', 'in:convenience, credit']
        ]);

        $item->buyer_id = Auth::id();
        $item->save();

        return redirect()->route('purchase.show', ['item' => $item->id])->with('success', '購入確認画面へ遷移しました。');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;


class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $tab = $request->query('tab', 'sell');

        $items = Item::where('user_id', $user->id)->latest()->get();
        $purchasedItems = Item::where('buyer_id', $user->id)->latest()->get();

        return view('mypage.index', compact('user', 'items', 'purchasedItems', 'tab'));
    }
}

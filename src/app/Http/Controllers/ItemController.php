<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'recommend'); 

        if ($tab === 'mylist' && Auth::check()) {
            
            $items = Item::where('buyer_id', Auth::id())->latest()->get();
        } else {
            
            $query = Item::with('user', 'buyer');

            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }

            $items = $query->latest()->get();
        }

        return view('items.index', [
            'items' => $items,
            'tab' => $tab,
        ]);
    }

    public function mylist()
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        $likedItemIds = $user->likes()->pluck('item_id');

        $items = Item::with('user', 'buyer')
            ->whereIn('id', $likedItemIds)
            ->latest()
            ->get();

        return view('items.mylist', [
            'items' => $items,
            'likedItemIds' => $likedItemIds,
        ]);
    }

    public function show(Item $item)
    {
        $item->load('comments.user', 'categories', 'likedByUsers')->loadCount('likedByUsers');

        return view('items.show', compact('item'));
    }
}

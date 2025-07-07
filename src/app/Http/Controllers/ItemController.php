<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        $query = Item::with('user', 'buyer');
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        $items = $query->latest()->get();

        return view('items.index', compact('items'));
    }
}

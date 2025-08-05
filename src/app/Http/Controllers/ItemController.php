<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'recommend');

        if ($tab === 'mylist' && Auth::check()) {
            $items = Auth::user()->likedItems()
                ->with(['user', 'buyer', 'comments.user'])
                ->latest()
                ->get();
        } else {
            $query = Item::with(['user', 'buyer', 'comments.user']);

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

    public function sell()
    {
        $categories = Category::all();
        $selected = session('selected_categories', []);

        return view('items.sell', compact('categories', 'selected'));
    }

    public function store(ExhibitionRequest $request, Item $item)
    {
        $validated = $request->validated();

        $imagePath = $request->file('image')->store('images', 'publlic');

        $item = Item::create([
            'user_id'       => auth()->id(),
            'image_path'    => $imagePath,
            'condition'     => $validated['condition'],
            'name'          => $validated['name'],
            'brand'         => $validated['brand'] ?? null,
            'description'   => $validated['description'],
            'price'         => $validated['price'],
        ]);

        $item->categories()->attach($validated['categories']);

        return redirect()->route('items.index')->with('success', '商品を出品しました。');
    }
}

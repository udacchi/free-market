<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image'       => ['image', 'max:2048'],
            'name'        => ['required', 'string', 'max:255'],
            'brand'       => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price'       => ['required', 'integer', 'min:1'],
            'condition'   => ['required', 'string'],
            'categories'  => ['required', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ]);

        session(['selected_categories' => $validated['categories']]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $item = Item::create([
            'user_id'    => Auth::id(),
            'name'       => $validated['name'],
            'brand'      => $validated['brand'] ?? null,
            'description' => $validated['description'],
            'price'      => $validated['price'],
            'condition'  => $validated['condition'],
            'image_path' => $imagePath,
        ]);

        $item->categories()->sync($validated['categories']);

        return redirect()->route('items.index')->with('success', '商品を出品しました。');
    }
}

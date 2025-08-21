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
        $tab     = $request->get('tab', 'recommend');
        $keyword = trim((string) $request->query('keyword')); // ← 追加

        if ($tab === 'mylist') {
            if (!Auth::check()) {
                $items = collect(); // 未認証は空表示仕様のまま
            } else {
                $items = Auth::user()
                    ->likedItems()                              // いいね商品
                    ->where('items.user_id', '!=', Auth::id())  // 自分の出品は除外
                    ->when($keyword !== '', function ($q) use ($keyword) { // ← 追加
                        // likedItems() は items テーブルが基点なので items.name を明示
                        $q->where('items.name', 'like', "%{$keyword}%");
                    })
                    ->with(['user', 'buyer', 'comments.user'])
                    ->latest()
                    ->get();
            }
        } else {
            $query = Item::with(['user', 'buyer', 'comments.user']);

            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id()); // 自分の出品は除外
            }

            // ← 追加：通常タブでもキーワードで絞り込み
            if ($keyword !== '') {
                $query->where('name', 'like', "%{$keyword}%");
            }

            $items = $query->orderBy('id', 'asc')->get();
        }

        // ← 検索値をビューで使えるように渡す（inputのvalueに利用）
        return view('items.index', ['items' => $items, 'tab' => $tab, 'keyword' => $keyword]);
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
        $item->load(['comments.user', 'categories']);
        $item->loadCount('likes');

        $isLiked = auth()->check()
            ? $item->likes()->where('user_id', auth()->id())->exists()
            : false;

        return view('items.show', compact('item', 'isLiked'));
    }

    public function sell()
    {
        $categories = Category::all()->unique('name')->sortBy('id')->values();
        $selected = session('selected_categories', []);

        return view('items.sell', compact('categories', 'selected'));
    }

    public function store(ExhibitionRequest $request, Item $item)
    {
        $validated = $request->validated();

        $imagePath = $request->file('image')->store('images', 'public');

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

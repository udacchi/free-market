<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:500'],
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'body' => $request->body,
        ]);

        return redirect()->back()->with('success', 'コメントを投稿しました。');
    }
}

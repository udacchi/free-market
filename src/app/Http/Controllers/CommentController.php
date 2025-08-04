<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        $validated = $request->validated();

        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'body' => $validated['comment'],
        ]);

        return redirect()->back()->with('success', 'コメントを投稿しました。');
    }
}

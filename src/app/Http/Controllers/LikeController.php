<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Item $item)
    {
        $user = Auth::user();

        if ($user->likedItems()->where('item_id', $item->id)->exists()) {
            $user->likedItems()->detach($item->id);
        } else {
            $user->likedItems()->attach($item->id);
        }

        return response()->json(['status' => 'success']);
    }
}

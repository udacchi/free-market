<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('mypage', compact('user'));
    }
    
    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'postal' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        $user->name = $request->input('name');
        $user->postal = $request->input('postal');
        $user->address = $request->input('address');
        $user->building = $request->input('building');

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return redirect('/');
    }

    public function updateAddress(Request $request)
    {
        $request->validate([
            'postal' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'postal' => $request->postal,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        // 商品IDが渡されていれば、購入画面へ戻る
        if ($request->filled('redirect_item_id')) {
            return redirect()->route('purchase.address', ['item' => $request->redirect_item_id])
                ->with('success', '住所を更新しました。');
        }

        // そうでなければ通常のマイページに戻るなど
        return redirect()->route('purchase')->with('success', '住所を更新しました。');
    }
}

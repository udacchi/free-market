<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;

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

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // プロフィール画像の処理
        if ($request->hasFile('avatar')) {
            // 既存画像があれば削除
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // 新しい画像を保存
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // その他のプロフィール情報を更新
        $user->name     = $request->input('name', $user->name);
        $user->postal   = $request->input('postal', $user->postal);
        $user->address  = $request->input('address', $user->address);
        $user->building = $request->input('building', $user->building);

        $user->save();

        return redirect()->back()->with('success', 'プロフィールを更新しました。');
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

        if ($request->filled('redirect_item_id')) {
            return redirect()->route('purchase.address', ['item' => $request->redirect_item_id])
                ->with('success', '住所を更新しました。');
        }

        return redirect()->route('purchase')->with('success', '住所を更新しました。');
    }
}

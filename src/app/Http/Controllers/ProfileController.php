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
        $user = $request->user();
        $data = $request->validated(); // ProfileRequestの検証済みデータ

        // 画像アップロード
        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $update = [];

        if (array_key_exists('name', $data)) {
            $update['name'] = $data['name']; // name は必須想定（ProfileRequestでrequiredに）
        }

        foreach (['postal', 'address', 'building'] as $k) {
            if (array_key_exists($k, $data) && $data[$k] !== null && $data[$k] !== '') {
                $update[$k] = $data[$k];
            }
        }

        if (array_key_exists('avatar', $data)) {
            $update['avatar'] = $data['avatar'];
        }

        if (!empty($update)) {
            $user->fill($update)->save();
        }

        return redirect()->route('items.index')->with('success', 'プロフィールを更新しました。');
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

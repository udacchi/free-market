<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('mypage');
    }
    
    public function edit()
    {
        return view('mypage.profile');
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
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect('/');
    }
}

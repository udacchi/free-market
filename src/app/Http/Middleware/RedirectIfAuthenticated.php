<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        //foreach ($guards as $guard) {
        //if (Auth::guard($guard)->check()) {

        // ✅ メール未認証の場合は verify-email ページへ
        //if (!Auth::user()->hasVerifiedEmail()) {
        //return redirect()->route('verification.notice');
        //}

        // ✅ 認証済みの場合はプロフィール編集画面へ
        //return redirect()->route('mypage.profile.edit');
        //}
        return $next($request);
    }
}

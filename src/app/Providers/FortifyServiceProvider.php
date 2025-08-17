<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Actions\Fortify\LogoutResponse as CustomLogoutResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LogoutResponse::class, CustomLogoutResponse::class);
    }

    public function boot(): void
    {
        // ビュー指定
        Fortify::registerView(fn() => view('auth.register'));
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::verifyEmailView(fn() => view('auth.verify-email'));

        // ユーザー作成
        Fortify::createUsersUsing(CreateNewUser::class);

        // ログインレート制限
        RateLimiter::for('login', fn(Request $request) => Limit::perMinute(10)->by($request->email . $request->ip()));

        // 登録後リダイレクト
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('verification.notice'); // 登録直後はメール確認ページ
                }
            };
        });

        // ログイン後リダイレクト
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\LoginResponse {
                public function toResponse($request)
                {
                    // メール未認証なら verify-email へ
                    if (!$request->user()->hasVerifiedEmail()) {
                        return redirect()->route('verification.notice');
                    }

                    // 認証済みならプロフィール編集画面
                    return redirect()->route('items.index');
                }
            };
        });
    }
}

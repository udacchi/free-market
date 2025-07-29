<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Actions\Fortify\LogoutResponse as CustomLogoutResponse;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use App\Http\Responses\VerifyEmailViewResponse as CustomVerifyEmailViewResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        // カスタムログアウトレスポンス
        $this->app->singleton(LogoutResponse::class, CustomLogoutResponse::class);
        $this->app->singleton(
            VerifyEmailViewResponse::class,
            CustomVerifyEmailViewResponse::class
        );
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        // 登録・ログインビューの指定
        Fortify::registerView(fn() => view('auth.register'));
        Fortify::loginView(fn() => view('auth.login'));

        // ユーザー作成ロジック
        Fortify::createUsersUsing(CreateNewUser::class);

        // ログインレート制限
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->email . $request->ip());
        });

        // ✅ 登録後のリダイレクト先
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect('/mypage/profile');
                }
            };
        });

        // ✅ ログイン後のリダイレクト先
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return redirect('/');
                }
            };
        });
    }
}

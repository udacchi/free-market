<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified as Middleware;

class CustomEnsureEmailIsVerified extends Middleware
{
    protected function redirectTo($request)
    {
        return route('mypage.profile.edit');
    }
}

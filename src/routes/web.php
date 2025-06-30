<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/', [AuthController::class, 'index']);
Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
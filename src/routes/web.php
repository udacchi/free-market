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
Route::get('/mypage', function () {
    return view('mypage');
})->middleware('authenticatedonly');


Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

Route::get('/mypage', [ProfileController::class, 'index'])->middleware('authenticatedonly')->name('mypage');
Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');

Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');
});

Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::post('/comments/{item}', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');

Route::post('/items/{item}/like', [LikeController::class, 'toggle'])->middleware('auth')->name('items.like');
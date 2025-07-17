<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MypageController;


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
Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');

Route::post('/items/{item}/like', [LikeController::class, 'toggle'])->middleware('auth')->name('items.like');

Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
});


Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');    //変更必要
Route::get('/mypage', [MypageController::class, 'index'])->middleware('auth')->name('mypage');
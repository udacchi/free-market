<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\RegisteredUserController;

/* Public */
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
Route::get('/search', [ItemController::class, 'index'])->name('search');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

/* Auth only（未認証メールもOK：LikeTestはこちら） */
Route::middleware('auth')->group(function () {
    // マイページ & プロフィール編集
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');

    // コメント・いいね（← verified を掛けない）
    Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/items/{item}/like', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/items/{item}/like', [LikeController::class, 'destroy'])->name('likes.destroy');
});

/* Auth + Verified（メール認証が必要な領域） */
Route::middleware(['auth', 'verified'])->group(function () {
    // 商品購入フロー
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/address/{item}/edit', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'showAddress'])->name('purchase.address');

    // 出品
    Route::get('/sell', [ItemController::class, 'sell'])->name('items.sell');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
});


// ログイン後のみアクセス可能なルート
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
});


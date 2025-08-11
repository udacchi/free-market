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

// トップページ（公開）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// ログイン・登録
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');

// ログイン後のみアクセス可能なルート
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
});

// 検索フォーム
Route::get('/search', [ItemController::class, 'index'])->name('search');

// 商品詳細
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// ログイン必須エリア
Route::middleware('auth')->group(function () {

    // マイページ
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');

    // コメント・いいね
    Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/items/{item}/like', [LikeController::class, 'toggle'])->name('items.like');

    // 商品購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 配送先住所編集フロー（購入用）
    Route::get('/purchase/address/{item}/edit', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit'); // 編集フォーム表示
    Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');  // フォーム送信処理
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'showAddress'])->name('purchase.address');           // 住所変更後の確認表示
    //商品の出品
    Route::get('/sell', [ItemController::class, 'sell'])->name('items.sell');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
});


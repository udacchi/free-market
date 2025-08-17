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
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/* ========================
 | メール認証関連
 ======================== */

// 認証前に表示する確認画面
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // あなたの verify-email.blade.php
})->middleware('auth')->name('verification.notice');

// メール認証リンクをクリックした時の処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // メール認証完了

    // ✅ 認証完了後は必ずプロフィール編集画面にリダイレクト
    return redirect()->route('mypage.profile.edit');
})->middleware(['auth', 'signed'])->name('verification.verify');

/* ========================
 | Public
 ======================== */
// トップ & 検索 & 商品詳細
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/search', [ItemController::class, 'index'])->name('search');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

// 認証画面
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.show');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');

/* ========================
 | Auth only（メール未認証でもOK）
 ======================== */
Route::middleware('auth')->group(function () {
    // プロフィール編集画面
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // プロフィール更新
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    // コメント
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

    // いいね（テストが参照する名前に統一）
    Route::post('/items/{item}/like', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/items/{item}/like', [LikeController::class, 'destroy'])->name('likes.destroy');

    // 出品
    Route::get('/sell', [ItemController::class, 'sell'])->name('items.sell');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');

    // 購入フロー
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // プレビュー（テスト対応。コントローラに preview() を実装）
    Route::get('/purchase/{item}/preview', [PurchaseController::class, 'preview'])->name('purchase.preview');

    // 配送先住所（購入用）
    Route::get('/purchase/address/{item}/edit', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'showAddress'])->name('purchase.address');
});

/* ========================
 | Auth + Verified
 ======================== */
Route::middleware(['auth', 'verified'])->group(function () {
    // マイページ
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');
});
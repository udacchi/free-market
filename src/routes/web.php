<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

Route::get('/mypage', [ProfileController::class, 'index'])->middleware('authenticatedonly')->name('mypage');
Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
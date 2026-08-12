<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Login Guru / Admin
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Login Orang Tua (NISN)
Route::get('/login-orangtua', [AuthController::class, 'showLoginOrangTua'])->name('login.orangtua');
Route::post('/login-orangtua', [AuthController::class, 'loginOrangTua'])->name('login.orangtua.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Login Guru / Admin
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Login Orang Tua (NISN)
Route::post('/login-orangtua', [AuthController::class, 'loginOrangTua'])->name('login.orangtua.post');

// Login Wali Kelas
Route::post('/login-walikelas', [AuthController::class, 'loginWaliKelas'])->name('login.walikelas.post');

// Login Waka Kesiswaan / Kepala Sekolah / Satpam
Route::post('/login-peran', [AuthController::class, 'loginPeran'])->name('login.peran.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

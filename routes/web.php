<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Route utama aplikasi PresensiKita.
| Routes dipisah per domain untuk memudahkan pemeliharaan:
|   - auth.php     : Login & logout
|   - guru.php     : Halaman & fitur untuk role Guru
|   - admin.php    : Halaman & fitur untuk role Admin
|   - orangtua.php : Halaman & fitur untuk role Orang Tua
|
*/

use App\Http\Controllers\Admin\LaporanMasukController;

require __DIR__ . '/auth.php';
require __DIR__ . '/guru.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/orangtua.php';
require __DIR__ . '/struktural.php';

// Form Laporan Pengaduan (Di Luar Login)
Route::get('/buat-laporan', [LaporanMasukController::class, 'showPublicForm'])->name('laporan.public');
Route::post('/buat-laporan', [LaporanMasukController::class, 'storePublic'])->name('laporan.public.store');


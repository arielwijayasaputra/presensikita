<?php

use App\Http\Controllers\OrangTua\AbsensiController;
use Illuminate\Support\Facades\Route;

// Semua route orang tua memerlukan middleware auth.orangtua
Route::middleware('auth.orangtua')->group(function () {
    Route::get('/orangtua', [AbsensiController::class, 'index'])->name('orangtua.index');
    Route::get('/orangtua/realtime', [AbsensiController::class, 'realtime'])->name('orangtua.realtime');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Struktural\DashboardController;
use App\Http\Controllers\Admin\PengaturanController;

Route::middleware('auth.struktural')->group(function () {
    Route::get('/waka', [DashboardController::class, 'index'])->name('waka.index');
    Route::get('/kepsek', [DashboardController::class, 'index'])->name('kepsek.index');
    Route::get('/satpam', [DashboardController::class, 'index'])->name('satpam.index');
    Route::get('/guru-piket', [DashboardController::class, 'index'])->name('gurupiket.index');
    Route::get('/wali-kelas', [DashboardController::class, 'index'])->name('walikelas.index');

    Route::post('/struktural/profil/update', [PengaturanController::class, 'updateProfil'])->name('struktural.profil.update');
});

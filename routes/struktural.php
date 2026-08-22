<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Struktural\DashboardController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\IzinGuruController;

Route::middleware('auth.struktural')->group(function () {
    Route::get('/waka', [DashboardController::class, 'index'])->name('waka.index');
    Route::get('/kepsek', [DashboardController::class, 'index'])->name('kepsek.index');
    Route::get('/satpam', [DashboardController::class, 'index'])->name('satpam.index');
    Route::get('/guru-piket', [DashboardController::class, 'index'])->name('gurupiket.index');
    Route::get('/wali-kelas', [DashboardController::class, 'index'])->name('walikelas.index');

    Route::post('/struktural/profil/update', [PengaturanController::class, 'updateProfil'])->name('struktural.profil.update');
    Route::post('/guru-piket/izin-guru', [IzinGuruController::class, 'store'])->name('izin-guru.store');
    Route::get('/guru-piket/izin-guru', [IzinGuruController::class, 'form'])->name('izin-guru.form');
});

Route::get('/persetujuan-izin-guru/{izin}', [IzinGuruController::class, 'publicShow'])
    ->middleware('signed')
    ->name('izin-guru.public');

Route::get('/persetujuan-izin-guru/{izin}/{role}', [IzinGuruController::class, 'publicShow'])
    ->middleware('signed')
    ->name('izin-guru.public.role');

Route::post('/persetujuan-izin-guru/{izin}/{role}', [IzinGuruController::class, 'approve'])
    ->middleware('signed')
    ->name('izin-guru.approve');

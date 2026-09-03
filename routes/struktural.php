<?php

use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\DispenSiswaController;
use App\Http\Controllers\IzinGuruController;
use App\Http\Controllers\Struktural\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.struktural')->group(function () {
    Route::get('/waka', [DashboardController::class, 'index'])->name('waka.index');
    Route::get('/kepsek', [DashboardController::class, 'index'])->name('kepsek.index');
    Route::get('/satpam', [DashboardController::class, 'index'])->name('satpam.index');
    Route::get('/guru-piket', [DashboardController::class, 'index'])->name('gurupiket.index');
    Route::get('/wali-kelas', [DashboardController::class, 'index'])->name('walikelas.index');
    Route::get('/wali-kelas/export-rekap', [DashboardController::class, 'exportRekapKelasWali'])->name('walikelas.export');
    Route::get('/wali-kelas/export-rekap-absensi', [DashboardController::class, 'exportRekapAbsensiWali'])->name('walikelas.export-absensi');
    Route::get('/wali-kelas/export-rekap-jurnal', [DashboardController::class, 'exportRekapJurnalWali'])->name('walikelas.export-jurnal');
    Route::post('/wali-kelas/simpan-absensi-harian', [DashboardController::class, 'simpanAbsensiHarianWali'])->name('walikelas.simpan-absensi');
    Route::get('/waka-sdm', [DashboardController::class, 'index'])->name('wakasdm.index');
    Route::get('/waka-sdm/export-rekap', [DashboardController::class, 'exportRekapGuru'])->name('wakasdm.export');

    Route::post('/struktural/profil/update', [PengaturanController::class, 'updateProfil'])->name('struktural.profil.update');
    Route::post('/guru-piket/izin-guru', [IzinGuruController::class, 'store'])->name('izin-guru.store');
    Route::get('/guru-piket/izin-guru', [IzinGuruController::class, 'form'])->name('izin-guru.form');
    Route::get('/guru-piket/dispen-siswa', [DispenSiswaController::class, 'form'])->name('dispen-siswa.form');
    Route::post('/guru-piket/dispen-siswa', [DispenSiswaController::class, 'store'])->name('dispen-siswa.store');
    Route::post('/guru-piket/absensi-siswa', [DispenSiswaController::class, 'storeAbsensi'])->name('absensi-siswa.store');

    Route::post('/satpam/izinkan-keluar/{dispen}', [DashboardController::class, 'izinkanKeluar'])->name('satpam.izinkan-keluar');
    Route::post('/satpam/izinkan-masuk/{dispen}', [DashboardController::class, 'izinkanMasuk'])->name('satpam.izinkan-masuk');
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

Route::get('/persetujuan-dispen-siswa/{dispen}/{role}', [DispenSiswaController::class, 'publicShow'])
    ->middleware('signed')->name('dispen-siswa.public');
Route::post('/persetujuan-dispen-siswa/{dispen}/{role}', [DispenSiswaController::class, 'approve'])
    ->middleware('signed')->name('dispen-siswa.approve');

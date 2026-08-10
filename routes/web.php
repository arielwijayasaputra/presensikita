<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;

// Auth routes (guest only)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Guru routes (login required)
Route::middleware('auth.guru')->group(function () {
    Route::get('/', [AbsensiController::class, 'guruIndex'])->name('guru.index');
    Route::get('/absensi/siswa/{id_kelas}', [AbsensiController::class, 'getSiswa'])->name('absensi.siswa');
    Route::post('/absensi/simpan', [AbsensiController::class, 'simpanAbsensi'])->name('absensi.simpan');
});

// Admin routes (login + admin role required)
Route::middleware('auth.admin')->group(function () {
    Route::get('/admin', [AbsensiController::class, 'index'])->name('admin.index');
    Route::get('/laporan/data', [AbsensiController::class, 'getLaporanData'])->name('laporan.data');
    Route::post('/siswa/tambah', [AbsensiController::class, 'storeSiswa'])->name('siswa.tambah');
    Route::delete('/siswa/{id}', [AbsensiController::class, 'destroySiswa'])->name('siswa.hapus');
    Route::post('/kelas/tambah', [AbsensiController::class, 'storeKelas'])->name('kelas.tambah');
    Route::delete('/kelas/{id}', [AbsensiController::class, 'destroyKelas'])->name('kelas.hapus');
    Route::post('/mapel/tambah', [AbsensiController::class, 'storeMapel'])->name('mapel.tambah');
    Route::delete('/mapel/{id}', [AbsensiController::class, 'destroyMapel'])->name('mapel.hapus');
    Route::post('/guru/tambah', [AbsensiController::class, 'storeGuru'])->name('guru.tambah');
    Route::delete('/guru/{id}', [AbsensiController::class, 'destroyGuru'])->name('guru.hapus');
    Route::post('/pengaturan/update', [AbsensiController::class, 'updatePengaturan'])->name('pengaturan.update');
    Route::post('/profil/update', [AbsensiController::class, 'updateProfil'])->name('profil.update');
});


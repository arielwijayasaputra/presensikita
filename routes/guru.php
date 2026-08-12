<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guru\AbsensiController;

// Semua route guru memerlukan middleware auth.guru
Route::middleware('auth.guru')->group(function () {
    Route::get('/', [AbsensiController::class, 'index'])->name('guru.index');
    Route::get('/absensi/siswa/{id_kelas}', [AbsensiController::class, 'getSiswa'])->name('absensi.siswa');
    Route::post('/absensi/simpan', [AbsensiController::class, 'simpanAbsensi'])->name('absensi.simpan');
});

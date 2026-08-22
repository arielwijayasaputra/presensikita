<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guru\AbsensiController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\NotifikasiController;
use App\Http\Controllers\IzinGuruController;

// Semua route guru memerlukan middleware auth.guru
Route::middleware('auth.guru')->group(function () {
    Route::get('/', [AbsensiController::class, 'index'])->name('guru.index');
    Route::get('/absensi/siswa/{id_kelas}', [AbsensiController::class, 'getSiswa'])->name('absensi.siswa');
    Route::get('/absensi/cek', [AbsensiController::class, 'cekAbsensi'])->name('absensi.cek');
    Route::post('/absensi/simpan', [AbsensiController::class, 'simpanAbsensi'])->name('absensi.simpan');
    Route::post('/guru/profil/update', [PengaturanController::class, 'updateProfil'])->name('guru.profil.update');
    Route::get('/guru/izin-guru', [IzinGuruController::class, 'form'])->name('guru.izin-guru.form');
    Route::post('/guru/izin-guru', [IzinGuruController::class, 'store'])->name('guru.izin-guru.store');

    // Laporan (dapat diakses admin maupun guru)
    Route::get('/laporan/data', [LaporanController::class, 'getData'])->name('laporan.data');

    // Notifikasi (dapat diakses semua guru & admin)
    Route::get('/admin/notifikasi',        [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/admin/notifikasi/read',  [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
});

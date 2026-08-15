<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\NotifikasiController;

// Semua route admin memerlukan middleware auth.admin
Route::middleware('auth.admin')->group(function () {

    // Dashboard
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.index');

    // CRUD Siswa
    Route::post('/siswa/tambah', [SiswaController::class, 'store'])->name('siswa.tambah');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.hapus');

    // CRUD Kelas
    Route::post('/kelas/tambah', [KelasController::class, 'store'])->name('kelas.tambah');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.hapus');

    // CRUD Mapel
    Route::post('/mapel/tambah', [MapelController::class, 'store'])->name('mapel.tambah');
    Route::delete('/mapel/{id}', [MapelController::class, 'destroy'])->name('mapel.hapus');

    // CRUD Guru
    Route::post('/guru/tambah', [GuruController::class, 'store'])->name('guru.tambah');
    Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('guru.hapus');

    // Pengaturan & Profil
    Route::post('/pengaturan/update', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::post('/profil/update', [PengaturanController::class, 'updateProfil'])->name('profil.update');

    // Notifikasi
    Route::get('/admin/notifikasi',        [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/admin/notifikasi/read',  [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
});

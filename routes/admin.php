<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\GuruPiketController;
use App\Http\Controllers\Admin\JadwalMengajarController;
use App\Http\Controllers\Admin\JamPelajaranController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\LaporanMasukController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\NaikKelasController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\SiswaController;
use Illuminate\Support\Facades\Route;

// Semua route admin memerlukan middleware auth.admin
Route::middleware('auth.admin')->group(function () {

    // Dashboard
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.index');

    // CRUD Siswa
    Route::post('/siswa/tambah', [SiswaController::class, 'store'])->name('siswa.tambah');
    Route::post('/siswa/import-csv', [SiswaController::class, 'importCsv'])->name('siswa.import');
    Route::post('/siswa/hapus-semua', [SiswaController::class, 'hapusSemua'])->name('siswa.hapus-semua');
    Route::post('/siswa/{id}/update', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.hapus');

    // CRUD Kelas
    Route::post('/kelas/tambah', [KelasController::class, 'store'])->name('kelas.tambah');
    Route::post('/kelas/{id}/update', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.hapus');

    // CRUD Jurusan
    Route::post('/jurusan/tambah', [JurusanController::class, 'store'])->name('jurusan.tambah');
    Route::post('/jurusan/{id}/update', [JurusanController::class, 'update'])->name('jurusan.update');
    Route::patch('/jurusan/{id}/toggle-aktif', [JurusanController::class, 'toggleAktif'])->name('jurusan.toggle');
    Route::delete('/jurusan/{id}', [JurusanController::class, 'destroy'])->name('jurusan.hapus');

    // CRUD Mapel
    Route::post('/mapel/tambah', [MapelController::class, 'store'])->name('mapel.tambah');
    Route::post('/mapel/{id}/update', [MapelController::class, 'update'])->name('mapel.update');
    Route::delete('/mapel/{id}', [MapelController::class, 'destroy'])->name('mapel.hapus');

    // CRUD Guru
    Route::post('/guru/tambah', [GuruController::class, 'store'])->name('guru.tambah');
    Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import');
    Route::post('/guru/hapus-semua', [GuruController::class, 'hapusSemua'])->name('guru.hapus-semua');
    Route::post('/guru/{id}/update', [GuruController::class, 'update'])->name('guru.update');
    Route::patch('/guru/{id}/toggle-aktif', [GuruController::class, 'toggleAktif'])->name('guru.toggle');
    Route::post('/guru/{id}/alihkan-jadwal', [GuruController::class, 'alihkanJadwal'])->name('guru.alihkan-jadwal');
    Route::post('/guru/{id}/kosongkan-jadwal', [GuruController::class, 'kosongkanJadwal'])->name('guru.kosongkan-jadwal');
    Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('guru.hapus');

    // Penugasan Guru Piket harian
    Route::post('/guru-piket/update', [GuruPiketController::class, 'update'])->name('guru-piket.update');
    Route::post('/guru-piket/update-bulk', [GuruPiketController::class, 'updateBulk'])->name('guru-piket.update-bulk');
    Route::post('/jam-pelajaran/update', [JamPelajaranController::class, 'update'])->name('jam-pelajaran.update');
    Route::post('/jam-pelajaran/{id}/update', [JamPelajaranController::class, 'updateSingle'])->name('jam-pelajaran.update-single');
    Route::post('/jam-pelajaran/istirahat/{hari}/{nomor}', [JamPelajaranController::class, 'updateIstirahat'])->name('jam-pelajaran.update-istirahat');
    Route::get('/jadwal/guru-tersedia', [JadwalMengajarController::class, 'guruTersedia'])->name('jadwal.guru-tersedia');
    Route::post('/jadwal/{id}/tugaskan-guru', [JadwalMengajarController::class, 'tugaskanGuru'])->name('jadwal.tugaskan-guru');
    Route::post('/jadwal/import', [JadwalMengajarController::class, 'import'])->name('jadwal.import');
    Route::delete('/jadwal/hapus-semua', [JadwalMengajarController::class, 'destroyAll'])->name('jadwal.hapus-semua');
    Route::post('/jadwal/tambah', [JadwalMengajarController::class, 'store'])->name('jadwal.tambah');
    Route::post('/jadwal/{id}/update', [JadwalMengajarController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [JadwalMengajarController::class, 'destroy'])->name('jadwal.hapus');

    // Pengaturan & Profil
    Route::post('/pengaturan/update', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::post('/profil/update', [PengaturanController::class, 'updateProfil'])->name('profil.update');

    // Naik Kelas
    Route::get('/naik-kelas', [NaikKelasController::class, 'index'])->name('naik-kelas.index');
    Route::get('/naik-kelas/preview', [NaikKelasController::class, 'preview'])->name('naik-kelas.preview');
    Route::post('/naik-kelas/execute', [NaikKelasController::class, 'execute'])->name('naik-kelas.execute');
    Route::get('/naik-kelas/siswa/{id_kelas}', [NaikKelasController::class, 'getSiswaByKelas'])->name('naik-kelas.siswa');
    Route::get('/alumni', [NaikKelasController::class, 'alumniList'])->name('alumni.list');

    // Manajemen Laporan Masuk
    Route::post('/laporan-masuk/{id}/status', [LaporanMasukController::class, 'updateStatus'])->name('laporan.update-status');
    Route::delete('/laporan-masuk/{id}', [LaporanMasukController::class, 'destroy'])->name('laporan.hapus');

});

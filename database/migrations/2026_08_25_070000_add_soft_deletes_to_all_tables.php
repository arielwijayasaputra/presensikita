<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'mapel',
        'jurusan',
        'jadwal_mengajar',
        'laporan',
        'pengumuman',
        'guru_piket',
        'jam_pelajaran',
        'dispen_siswa',
        'izin_guru',
        'jurnal_kelas',
        'jurnal_siswa_tidak_hadir',
        'tahun_ajaran',
        'notifikasi',
        'riwayat_naik_kelas',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};

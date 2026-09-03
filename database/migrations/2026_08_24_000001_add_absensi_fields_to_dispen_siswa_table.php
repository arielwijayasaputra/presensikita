<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->string('jenis_absen', 10)->default('I')->after('alasan');
            $table->string('foto_surat', 255)->nullable()->after('jenis_absen');
            $table->integer('id_jurnal')->nullable()->after('foto_surat');
            $table->index('id_jurnal');
        });
    }

    public function down(): void
    {
        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->dropIndex(['id_jurnal']);
            $table->dropColumn(['jenis_absen', 'foto_surat', 'id_jurnal']);
        });
    }
};

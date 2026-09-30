<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->string('tanda_tangan_kepsek', 255)->nullable()->after('catatan_kepsek');
            $table->string('tanda_tangan_waka', 255)->nullable()->after('catatan_waka');
        });

        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->string('tanda_tangan_waka', 255)->nullable()->after('catatan_waka');
        });
    }

    public function down(): void
    {
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->dropColumn(['tanda_tangan_kepsek', 'tanda_tangan_waka']);
        });

        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->dropColumn(['tanda_tangan_waka']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->date('tanggal_selesai')->nullable()->after('tanggal_dispen');
        });
    }

    public function down(): void
    {
        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->dropColumn('tanggal_selesai');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->timestamp('waktu_keluar')->nullable();
            $table->timestamp('waktu_masuk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->dropColumn(['waktu_keluar', 'waktu_masuk']);
        });
    }
};

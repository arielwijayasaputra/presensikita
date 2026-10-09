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
        Schema::table('hari_khusus', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('aturan_presensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hari_khusus', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('status_laporan', function (Blueprint $table) {
            $table->string('deskripsi_status', 255)->nullable()->after('slug_status');
        });
    }

    public function down(): void
    {
        Schema::table('status_laporan', function (Blueprint $table) {
            $table->dropColumn('deskripsi_status');
        });
    }
};

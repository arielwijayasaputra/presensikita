<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->string('kode_dispen', 50)->nullable()->after('id_dispen_siswa')->index();
        });
    }

    public function down(): void
    {
        Schema::table('dispen_siswa', function (Blueprint $table) {
            $table->dropIndex(['kode_dispen']);
            $table->dropColumn('kode_dispen');
        });
    }
};

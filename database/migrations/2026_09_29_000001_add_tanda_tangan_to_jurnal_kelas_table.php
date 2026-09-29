<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_kelas', function (Blueprint $table) {
            $table->string('tanda_tangan', 255)->nullable()->after('foto_selfie');
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_kelas', function (Blueprint $table) {
            $table->dropColumn('tanda_tangan');
        });
    }
};

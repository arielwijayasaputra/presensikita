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
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->integer('id_guru_piket')->nullable()->change();
            $table->string('status_konfirmasi_piket', 20)->default('menunggu')->after('foto_surat');
            $table->timestamp('dikonfirmasi_piket_pada')->nullable()->after('status_konfirmasi_piket');
            $table->text('catatan_piket')->nullable()->after('dikonfirmasi_piket_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->dropColumn(['status_konfirmasi_piket', 'dikonfirmasi_piket_pada', 'catatan_piket']);
        });
    }
};

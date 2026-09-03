<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_guru', function (Blueprint $table) {
            $table->id('id_izin_guru');
            $table->integer('id_guru');
            $table->integer('id_guru_piket');
            $table->date('tanggal_izin');
            $table->text('alasan');
            $table->string('status_kepsek', 20)->default('menunggu');
            $table->string('status_waka', 20)->default('menunggu');
            $table->text('catatan_kepsek')->nullable();
            $table->text('catatan_waka')->nullable();
            $table->timestamp('disetujui_kepsek_pada')->nullable();
            $table->timestamp('disetujui_waka_pada')->nullable();
            $table->timestamps();

            $table->foreign('id_guru')->references('id_guru')->on('guru')->cascadeOnDelete();
            $table->foreign('id_guru_piket')->references('id_guru')->on('guru')->cascadeOnDelete();
            $table->index(['tanggal_izin', 'id_guru']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_guru');
    }
};

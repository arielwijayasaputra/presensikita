<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispen_siswa', function (Blueprint $table) {
            $table->id('id_dispen_siswa');
            $table->integer('id_siswa');
            $table->integer('id_guru_piket');
            $table->date('tanggal_dispen');
            $table->text('alasan');
            $table->string('status_waka', 20)->default('menunggu');
            $table->string('status_guru_piket', 20)->default('menunggu');
            $table->text('catatan_waka')->nullable();
            $table->text('catatan_guru_piket')->nullable();
            $table->timestamp('disetujui_waka_pada')->nullable();
            $table->timestamp('disetujui_guru_piket_pada')->nullable();
            $table->timestamps();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->cascadeOnDelete();
            $table->foreign('id_guru_piket')->references('id_guru')->on('guru')->cascadeOnDelete();
            $table->index(['tanggal_dispen', 'id_siswa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispen_siswa');
    }
};

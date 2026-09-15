<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('keterlambatan_siswa');

        Schema::create('keterlambatan_siswa', function (Blueprint $table) {
            $table->id('id_keterlambatan');
            $table->integer('id_siswa');
            $table->integer('id_guru_piket')->nullable();
            $table->date('tanggal');
            $table->time('jam_masuk');
            $table->unsignedInteger('jam_ke')->default(1);
            $table->text('alasan')->nullable();
            $table->string('foto_surat', 255)->nullable();
            $table->enum('status', ['menunggu', 'diizinkan', 'ditolak'])->default('diizinkan');
            $table->timestamp('disetujui_pada')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->cascadeOnDelete();
            $table->foreign('id_guru_piket')->references('id_guru')->on('guru')->nullOnDelete();
            $table->index(['tanggal', 'id_siswa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keterlambatan_siswa');
    }
};

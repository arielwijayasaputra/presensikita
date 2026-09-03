<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->integer('id_laporan')->autoIncrement();
            $table->string('role_pelapor', 50);
            $table->string('nama_pelapor', 100);
            $table->string('judul', 150);
            $table->text('isi_laporan');
            $table->enum('status', ['menunggu', 'diterima', 'ditolak', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_siswa_tidak_hadir', function (Blueprint $table) {
            $table->integer('id_absen')->autoIncrement();
            $table->integer('id_jurnal');
            $table->integer('id_siswa');
            $table->enum('status', ['S', 'I', 'A']);
            $table->string('keterangan', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_siswa_tidak_hadir');
    }
};

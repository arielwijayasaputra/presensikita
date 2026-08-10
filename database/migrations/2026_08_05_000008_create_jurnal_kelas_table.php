<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_kelas', function (Blueprint $table) {
            $table->integer('id_jurnal')->autoIncrement();
            $table->integer('id_jadwal');
            $table->date('tanggal');
            $table->enum('status_kehadiran_guru', ['Hadir', 'Tidak Hadir'])->default('Hadir');
            $table->string('materi', 255)->nullable();
            $table->integer('jumlah_hadir')->default(0);
            $table->timestamp('waktu_input')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_kelas');
    }
};

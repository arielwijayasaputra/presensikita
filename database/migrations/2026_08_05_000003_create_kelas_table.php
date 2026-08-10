<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->integer('id_kelas')->autoIncrement();
            $table->string('nama_kelas', 50);
            $table->string('tingkat_kelas', 10);
            $table->string('jurusan', 50);
            $table->integer('id_tahun_ajaran');
            $table->integer('id_wali_kelas')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};

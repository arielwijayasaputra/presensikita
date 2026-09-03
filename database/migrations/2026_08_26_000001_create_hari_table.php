<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hari', function (Blueprint $table) {
            $table->integer('id_hari')->autoIncrement();
            $table->string('nama_hari', 20);
            $table->string('nama_inggris', 20);
            $table->string('singkatan', 5);
            $table->integer('urutan');
            $table->boolean('is_aktif')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hari');
    }
};

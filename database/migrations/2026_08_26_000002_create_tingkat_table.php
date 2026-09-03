<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tingkat', function (Blueprint $table) {
            $table->integer('id_tingkat')->autoIncrement();
            $table->string('nama_tingkat', 10);
            $table->integer('angka');
            $table->integer('urutan');
            $table->boolean('is_aktif')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tingkat');
    }
};

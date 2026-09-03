<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_laporan', function (Blueprint $table) {
            $table->integer('id_status')->autoIncrement();
            $table->string('nama_status', 50);
            $table->string('slug_status', 50)->unique();
            $table->integer('urutan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_laporan');
    }
};

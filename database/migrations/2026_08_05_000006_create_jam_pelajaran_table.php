<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_pelajaran', function (Blueprint $table) {
            $table->integer('id_jam')->autoIncrement();
            $table->integer('jam_ke')->unique();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_pelajaran');
    }
};

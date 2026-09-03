<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapel', function (Blueprint $table) {
            $table->integer('id_mapel')->autoIncrement();
            $table->string('kode_mapel', 20)->nullable()->unique();
            $table->string('nama_mapel', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapel');
    }
};

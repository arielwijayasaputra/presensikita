<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->integer('id_tahun_ajaran')->autoIncrement();
            $table->string('tahun_ajaran', 50);
            $table->string('semester', 20);
            $table->tinyInteger('is_aktif')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};

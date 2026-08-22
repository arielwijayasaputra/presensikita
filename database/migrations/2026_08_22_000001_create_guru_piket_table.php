<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_piket', function (Blueprint $table) {
            $table->bigIncrements('id_guru_piket');
            $table->integer('id_guru');
            $table->date('tanggal');
            $table->timestamps();

            $table->unique(['id_guru', 'tanggal']);
            $table->foreign('id_guru')->references('id_guru')->on('guru')->cascadeOnDelete();
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_piket');
    }
};

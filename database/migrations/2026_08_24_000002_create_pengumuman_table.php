<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id('id_pengumuman');
            $table->string('judul', 150);
            $table->text('isi');
            $table->string('target_role', 30)->default('semua');
            $table->boolean('is_aktif')->default(true);
            $table->integer('id_admin')->nullable();
            $table->timestamps();
            $table->index(['target_role', 'is_aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};

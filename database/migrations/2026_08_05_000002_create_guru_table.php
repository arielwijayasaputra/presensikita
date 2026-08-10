<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->integer('id_guru')->autoIncrement();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('nama_guru', 100);
            $table->enum('Peran', ['Guru', 'Wali Kelas'])->default('Guru');
            $table->string('no_hp', 20)->nullable();
            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->tinyInteger('is_admin')->default(0);
            $table->tinyInteger('is_aktif')->default(1);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hari_khusus', function (Blueprint $table) {
            $table->id('id_hari_khusus');
            $table->enum('tipe', ['event', 'pulang_cepat']);
            $table->string('judul');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->json('tingkat'); // Array tingkat: [10, 11, 12]
            $table->time('jam_pulang')->nullable();
            $table->enum('aturan_presensi', ['tetap_wajib', 'diliburkan', 'hadir_event'])->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hari_khusus');
    }
};

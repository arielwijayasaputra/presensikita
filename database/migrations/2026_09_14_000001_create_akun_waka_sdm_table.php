<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('akun_waka_sdm')) {
            Schema::create('akun_waka_sdm', function (Blueprint $table) {
                $table->increments('id_waka_sdm');
                $table->string('nama', 100)->default('Waka SDM');
                $table->string('username', 50)->unique();
                $table->string('password_hash', 255);
                $table->string('no_hp', 20)->nullable();
                $table->string('foto_profil', 255)->nullable();
                $table->tinyInteger('is_aktif')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Seed default waka sdm account if table is empty
        $count = DB::table('akun_waka_sdm')->count();
        if ($count === 0) {
            DB::table('akun_waka_sdm')->insert([
                'nama' => 'Waka SDM',
                'username' => 'wakasdm',
                'password_hash' => Hash::make('wakasdm123'),
                'no_hp' => null,
                'foto_profil' => null,
                'is_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('akun_waka_sdm');
    }
};
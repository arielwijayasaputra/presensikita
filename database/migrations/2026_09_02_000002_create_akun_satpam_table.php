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
        if (! Schema::hasTable('akun_satpam')) {
            Schema::create('akun_satpam', function (Blueprint $table) {
                $table->increments('id_satpam');
                $table->string('nama', 100)->default('Satpam');
                $table->string('username', 50)->unique();
                $table->string('password_hash', 255);
                $table->string('no_hp', 20)->nullable();
                $table->string('foto_profil', 255)->nullable();
                $table->tinyInteger('is_aktif')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Seed default satpam account if table is empty
        $count = DB::table('akun_satpam')->count();
        if ($count === 0) {
            DB::table('akun_satpam')->insert([
                'nama' => 'Satpam',
                'username' => 'satpam',
                'password_hash' => Hash::make('satpam123'),
                'no_hp' => null,
                'foto_profil' => null,
                'is_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Remove satpam from guru table if exists (no longer needed there)
        if (Schema::hasTable('guru')) {
            DB::table('guru')->where('Peran', 'Satpam')->delete();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('akun_satpam');
    }
};

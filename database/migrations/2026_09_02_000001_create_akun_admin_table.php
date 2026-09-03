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
        if (! Schema::hasTable('akun_admin')) {
            Schema::create('akun_admin', function (Blueprint $table) {
                $table->increments('id_admin');
                $table->string('nama', 100)->default('Administrator');
                $table->string('username', 50)->unique();
                $table->string('password', 255);
                $table->string('password_hash', 255)->nullable();
                $table->string('no_tlp', 20)->nullable();
                $table->string('no_hp', 20)->nullable();
                $table->string('foto_profil', 255)->nullable();
                $table->timestamp('update_pw_at')->nullable();
                $table->timestamp('update_usn_at')->nullable();
                $table->tinyInteger('is_aktif')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Migrate existing admin from guru table if exists
        $adminCount = DB::table('akun_admin')->count();
        if ($adminCount === 0) {
            $existingAdmin = DB::table('guru')->where('is_admin', 1)->first()
                ?? DB::table('guru')->where('username', 'admin')->first();

            if ($existingAdmin) {
                DB::table('akun_admin')->insert([
                    'nama' => $existingAdmin->nama_guru ?: 'Administrator',
                    'username' => $existingAdmin->username ?: 'admin',
                    'password' => $existingAdmin->password_hash ?: Hash::make('admin123'),
                    'password_hash' => $existingAdmin->password_hash ?: Hash::make('admin123'),
                    'no_tlp' => $existingAdmin->no_hp,
                    'no_hp' => $existingAdmin->no_hp,
                    'foto_profil' => $existingAdmin->foto_profil ?? null,
                    'update_pw_at' => null,
                    'update_usn_at' => null,
                    'is_aktif' => 1,
                    'created_at' => $existingAdmin->created_at ?? now(),
                    'updated_at' => now(),
                ]);

                // Remove the admin from the guru table so guru table only contains teachers
                DB::table('guru')->where('id_guru', $existingAdmin->id_guru)->delete();
            } else {
                $defaultPassword = Hash::make('admin123');
                DB::table('akun_admin')->insert([
                    'nama' => 'Administrator',
                    'username' => 'admin',
                    'password' => $defaultPassword,
                    'password_hash' => $defaultPassword,
                    'no_tlp' => null,
                    'no_hp' => null,
                    'foto_profil' => null,
                    'update_pw_at' => null,
                    'update_usn_at' => null,
                    'is_aktif' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('akun_admin');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')->insertOrIgnore([
            'id_role' => 8,
            'nama_role' => 'Waka SDM',
            'slug_role' => 'waka_sdm',
            'deskripsi' => 'Wakil Kepala Sekolah Bidang SDM yang mengontrol dan memantau kehadiran mengajar guru serta perizinan.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('guru')->insertOrIgnore([
            'nip' => '198001012025018800',
            'nama_guru' => 'Waka SDM (Kepegawaian)',
            'Peran' => 'Waka SDM',
            'username' => 'wakasdm',
            'password_hash' => Hash::make('wakasdm123'),
            'is_admin' => 0,
            'is_aktif' => 1,
            'created_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('guru')->where('username', 'wakasdm')->delete();
        DB::table('roles')->where('slug_role', 'waka_sdm')->delete();
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jam_pelajaran')) {
            $sabtuCount = DB::table('jam_pelajaran')->where('hari', 'Sabtu')->count();
            if ($sabtuCount === 0) {
                $seninRows = DB::table('jam_pelajaran')->where('hari', 'Senin')->get();
                foreach ($seninRows as $source) {
                    DB::table('jam_pelajaran')->insert([
                        'jam_ke' => $source->jam_ke,
                        'hari' => 'Sabtu',
                        'jam_mulai' => $source->jam_mulai,
                        'jam_selesai' => $source->jam_selesai,
                        'deleted_at' => $source->deleted_at,
                    ]);
                }
            }
        }

        if (Schema::hasTable('hari')) {
            DB::table('hari')->where('nama_hari', 'Sabtu')->update(['is_aktif' => 1]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('jam_pelajaran')) {
            DB::table('jam_pelajaran')->where('hari', 'Sabtu')->delete();
        }
    }
};

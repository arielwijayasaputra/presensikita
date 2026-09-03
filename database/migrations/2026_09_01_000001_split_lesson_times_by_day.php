<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('jam_pelajaran', 'hari')) {
            Schema::table('jam_pelajaran', function (Blueprint $table) {
                $table->string('hari', 10)->nullable()->after('jam_ke');
            });
        }

        DB::table('jam_pelajaran')->where('jam_ke', '>=', 100)->update(['hari' => 'Jumat']);
        DB::table('jam_pelajaran')->where('jam_ke', '<', 100)->update(['hari' => 'Senin']);
        foreach (Schema::getIndexes('jam_pelajaran') as $index) {
            if ($index['unique'] && $index['columns'] === ['jam_ke']) {
                Schema::table('jam_pelajaran', function (Blueprint $table) use ($index) {
                    $table->dropIndex($index['name']);
                });
            }
        }

        $weekdays = ['Selasa', 'Rabu', 'Kamis'];
        foreach ($weekdays as $hari) {
            $sourceRows = DB::table('jam_pelajaran')->where('hari', 'Senin')->get();
            foreach ($sourceRows as $source) {
                $newId = DB::table('jam_pelajaran')->insertGetId([
                    'jam_ke' => $source->jam_ke,
                    'hari' => $hari,
                    'jam_mulai' => $source->jam_mulai,
                    'jam_selesai' => $source->jam_selesai,
                    'deleted_at' => $source->deleted_at,
                ], 'id_jam');
                DB::table('jadwal_mengajar')
                    ->where('hari', $hari)
                    ->where('id_jam', $source->id_jam)
                    ->update(['id_jam' => $newId]);
            }
        }
    }

    public function down(): void
    {
        $seninIds = DB::table('jam_pelajaran')->where('hari', 'Senin')->pluck('id_jam');
        DB::table('jadwal_mengajar')->whereIn('id_jam', DB::table('jam_pelajaran')->whereIn('hari', ['Selasa', 'Rabu', 'Kamis'])->pluck('id_jam'))->update(['id_jam' => $seninIds->first()]);
        DB::table('jam_pelajaran')->whereIn('hari', ['Selasa', 'Rabu', 'Kamis'])->delete();
        Schema::table('jam_pelajaran', function (Blueprint $table) {
            $table->unique('jam_ke');
            $table->dropColumn('hari');
        });
    }
};

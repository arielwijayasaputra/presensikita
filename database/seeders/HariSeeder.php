<?php

namespace Database\Seeders;

use App\Models\Hari;
use Illuminate\Database\Seeder;

class HariSeeder extends Seeder
{
    public function run(): void
    {
        $hari = [
            ['nama_hari' => 'Senin',    'nama_inggris' => 'Monday',    'singkatan' => 'Sen', 'urutan' => 1],
            ['nama_hari' => 'Selasa',   'nama_inggris' => 'Tuesday',   'singkatan' => 'Sel', 'urutan' => 2],
            ['nama_hari' => 'Rabu',     'nama_inggris' => 'Wednesday', 'singkatan' => 'Rab', 'urutan' => 3],
            ['nama_hari' => 'Kamis',    'nama_inggris' => 'Thursday',  'singkatan' => 'Kam', 'urutan' => 4],
            ['nama_hari' => 'Jumat',    'nama_inggris' => 'Friday',    'singkatan' => 'Jum', 'urutan' => 5],
            ['nama_hari' => 'Sabtu',    'nama_inggris' => 'Saturday',  'singkatan' => 'Sab', 'urutan' => 6],
            ['nama_hari' => 'Minggu',   'nama_inggris' => 'Sunday',    'singkatan' => 'Min', 'urutan' => 7],
        ];

        foreach ($hari as $item) {
            Hari::updateOrCreate(
                ['nama_inggris' => $item['nama_inggris']],
                $item
            );
        }
    }
}

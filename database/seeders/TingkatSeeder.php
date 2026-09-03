<?php

namespace Database\Seeders;

use App\Models\Tingkat;
use Illuminate\Database\Seeder;

class TingkatSeeder extends Seeder
{
    public function run(): void
    {
        $tingkat = [
            ['nama_tingkat' => 'X',   'angka' => 10, 'urutan' => 1],
            ['nama_tingkat' => 'XI',  'angka' => 11, 'urutan' => 2],
            ['nama_tingkat' => 'XII', 'angka' => 12, 'urutan' => 3],
        ];

        foreach ($tingkat as $item) {
            Tingkat::updateOrCreate(
                ['nama_tingkat' => $item['nama_tingkat']],
                $item
            );
        }
    }
}

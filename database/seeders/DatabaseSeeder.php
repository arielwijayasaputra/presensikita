<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            HariSeeder::class,
            TingkatSeeder::class,
            StatusLaporanSeeder::class,
            SiswaSeeder::class,
            JurnalSeeder::class,
        ]);
    }
}

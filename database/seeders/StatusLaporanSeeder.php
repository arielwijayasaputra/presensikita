<?php

namespace Database\Seeders;

use App\Models\StatusLaporan;
use Illuminate\Database\Seeder;

class StatusLaporanSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['nama_status' => 'Menunggu',    'slug_status' => 'menunggu',    'urutan' => 1, 'deskripsi_status' => 'Status laporan dikembalikan ke MENUNGGU.'],
            ['nama_status' => 'Diterima',    'slug_status' => 'diterima',    'urutan' => 2, 'deskripsi_status' => 'Laporan telah DITERIMA. Anda dapat melanjutkannya ke proses.'],
            ['nama_status' => 'Ditolak',     'slug_status' => 'ditolak',     'urutan' => 3, 'deskripsi_status' => 'Laporan telah DITOLAK.'],
            ['nama_status' => 'Diproses',    'slug_status' => 'diproses',    'urutan' => 4, 'deskripsi_status' => 'Laporan sedang DIPROSES.'],
            ['nama_status' => 'Selesai',     'slug_status' => 'selesai',     'urutan' => 5, 'deskripsi_status' => 'Laporan telah SELESAI ditangani.'],
            ['nama_status' => 'Dibatalkan',  'slug_status' => 'dibatalkan',  'urutan' => 6, 'deskripsi_status' => 'Laporan telah DIBATALKAN.'],
        ];

        foreach ($statuses as $item) {
            StatusLaporan::updateOrCreate(
                ['slug_status' => $item['slug_status']],
                $item
            );
        }
    }
}

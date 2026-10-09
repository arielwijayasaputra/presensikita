<?php

namespace Database\Seeders;

use App\Models\HariKhusus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HariKhususSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();

        // 1. Contoh Event: Class Meeting / Dies Natalis (Semua Tingkat: 10, 11, 12) - Hadir Event
        HariKhusus::updateOrCreate(
            ['judul' => 'Class Meeting Semester Gasal'],
            [
                'tipe' => 'event',
                'tanggal_mulai' => $today->copy()->addDays(7)->toDateString(),
                'tanggal_selesai' => $today->copy()->addDays(9)->toDateString(),
                'jam_pulang' => null,
                'tingkat' => [10, 11, 12],
                'aturan_presensi' => 'hadir_event',
                'keterangan' => 'Kegiatan perlombaan antarkelas. Seluruh siswa otomatis tercatat Hadir Event.',
            ]
        );

        // 2. Contoh Event: Libur Khusus Awal Puasa / Hari Tenang (Tingkat 12 saja) - Diliburkan
        HariKhusus::updateOrCreate(
            ['judul' => 'Hari Tenang Persiapan Uji Kompetensi Keahlian'],
            [
                'tipe' => 'event',
                'tanggal_mulai' => $today->copy()->addDays(14)->toDateString(),
                'tanggal_selesai' => $today->copy()->addDays(14)->toDateString(),
                'jam_pulang' => null,
                'tingkat' => [12],
                'aturan_presensi' => 'diliburkan',
                'keterangan' => 'Belajar mandiri di rumah untuk kelas 12 sebelum UKK.',
            ]
        );

        // 3. Contoh Pulang Cepat: Rapat Pleno Guru / Persiapan Akreditasi (Tingkat 10 & 11) - Pulang pukul 11:30
        HariKhusus::updateOrCreate(
            ['judul' => 'Pulang Cepat - Rapat Pleno Dewan Guru'],
            [
                'tipe' => 'pulang_cepat',
                'tanggal_mulai' => $today->copy()->addDays(3)->toDateString(),
                'tanggal_selesai' => $today->copy()->addDays(3)->toDateString(),
                'jam_pulang' => '11:30:00',
                'tingkat' => [10, 11],
                'aturan_presensi' => null,
                'keterangan' => 'Pembelajaran selesai pukul 11:30 karena ada rapat pleno seluruh dewan guru.',
            ]
        );
    }
}

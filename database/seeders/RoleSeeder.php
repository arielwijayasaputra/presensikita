<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'id_role' => 1,
                'nama_role' => 'Guru',
                'slug_role' => 'guru',
                'deskripsi' => 'Tenaga pengajar yang melakukan absensi dan menginput jurnal pembelajaran harian.',
                'route_name' => 'guru.index',
                'is_struktural' => 0,
            ],
            [
                'id_role' => 2,
                'nama_role' => 'Wali Kelas',
                'slug_role' => 'walikelas',
                'deskripsi' => 'Guru yang bertanggung jawab memantau kehadiran dan perkembangan siswa di kelasnya.',
                'route_name' => 'walikelas.index',
                'is_struktural' => 1,
            ],
            [
                'id_role' => 3,
                'nama_role' => 'Guru Piket',
                'slug_role' => 'guru_piket',
                'deskripsi' => 'Guru bertugas memantau ketertiban, kehadiran guru dan siswa secara keseluruhan harian.',
                'route_name' => 'gurupiket.index',
                'is_struktural' => 1,
            ],
            [
                'id_role' => 4,
                'nama_role' => 'Waka',
                'slug_role' => 'waka',
                'deskripsi' => 'Wakil Kepala Sekolah yang memantau rekapitulasi presensi dan laporan kurikulum/kesiswaan.',
                'route_name' => 'waka.index',
                'is_struktural' => 1,
            ],
            [
                'id_role' => 5,
                'nama_role' => 'Kepsek',
                'slug_role' => 'kepsek',
                'deskripsi' => 'Kepala Sekolah yang memiliki akses peninjauan laporan eksekutif dan persetujuan sistem.',
                'route_name' => 'kepsek.index',
                'is_struktural' => 1,
            ],
            [
                'id_role' => 6,
                'nama_role' => 'Satpam',
                'slug_role' => 'satpam',
                'deskripsi' => 'Petugas keamanan sekolah yang memantau keluar-masuk siswa dan ketertiban area gerbang.',
                'route_name' => 'satpam.index',
                'is_struktural' => 1,
            ],
            [
                'id_role' => 7,
                'nama_role' => 'Admin',
                'slug_role' => 'admin',
                'deskripsi' => 'Administrator sistem yang mengelola data master (Siswa, Kelas, Guru, Mapel, Pengaturan).',
                'route_name' => 'admin.index',
                'is_struktural' => 0,
            ],
            [
                'id_role' => 8,
                'nama_role' => 'Waka SDM',
                'slug_role' => 'waka_sdm',
                'deskripsi' => 'Wakil Kepala Sekolah Bidang SDM yang memantau kehadiran guru.',
                'route_name' => 'wakasdm.index',
                'is_struktural' => 1,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug_role' => $roleData['slug_role']],
                $roleData
            );
        }
    }
}

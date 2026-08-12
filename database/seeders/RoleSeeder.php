<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'id_role'   => 1,
                'nama_role' => 'Guru',
                'slug_role' => 'guru',
                'deskripsi' => 'Tenaga pengajar yang melakukan absensi dan menginput jurnal pembelajaran harian.',
            ],
            [
                'id_role'   => 2,
                'nama_role' => 'Wali Kelas',
                'slug_role' => 'wali_kelas',
                'deskripsi' => 'Guru yang bertanggung jawab memantau kehadiran dan perkembangan siswa di kelasnya.',
            ],
            [
                'id_role'   => 3,
                'nama_role' => 'Guru Piket',
                'slug_role' => 'guru_piket',
                'deskripsi' => 'Guru bertugas memantau ketertiban, kehadiran guru dan siswa secara keseluruhan harian.',
            ],
            [
                'id_role'   => 4,
                'nama_role' => 'Waka',
                'slug_role' => 'waka',
                'deskripsi' => 'Wakil Kepala Sekolah yang memantau rekapitulasi presensi dan laporan kurikulum/kesiswaan.',
            ],
            [
                'id_role'   => 5,
                'nama_role' => 'Kepsek',
                'slug_role' => 'kepsek',
                'deskripsi' => 'Kepala Sekolah yang memiliki akses peninjauan laporan eksekutif dan persetujuan sistem.',
            ],
            [
                'id_role'   => 6,
                'nama_role' => 'Satpam',
                'slug_role' => 'satpam',
                'deskripsi' => 'Petugas keamanan sekolah yang memantau keluar-masuk siswa dan ketertiban area gerbang.',
            ],
            [
                'id_role'   => 7,
                'nama_role' => 'Admin',
                'slug_role' => 'admin',
                'deskripsi' => 'Administrator sistem yang mengelola data master (Siswa, Kelas, Guru, Mapel, Pengaturan).',
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

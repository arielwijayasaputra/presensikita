<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = ['Aditya', 'Aisyah', 'Bima', 'Citra', 'Daffa', 'Elsa', 'Fajar', 'Gita', 'Hendra', 'Indah', 'Joko', 'Kartika', 'Lukman', 'Maya', 'Naufal', 'Olivia', 'Pandu', 'Qori', 'Rizky', 'Sinta', 'Tegar', 'Ulfa', 'Vicky', 'Wulan', 'Yusuf', 'Zahra', 'Alif', 'Bella', 'Chandra', 'Dian'];
        $lastNames = ['Pratama', 'Putri', 'Saputra', 'Ananda', 'Al-Farizi', 'Maharani', 'Nugroho', 'Permata', 'Setiawan', 'Kurniawati', 'Susilo', 'Dewi', 'Hakim', 'Sari', 'Rahman', 'Wicaksono', 'Firmansyah', 'Rahayu', 'Prasojo', 'Khoirunisa', 'Rahardian', 'Safitri', 'Abdullah', 'Nurul', 'Hidayat'];

        $kelases = Kelas::all();
        foreach ($kelases as $kelas) {
            $existing = Siswa::where('id_kelas', $kelas->id_kelas)->count();
            if ($existing < 10) {
                for ($i = $existing + 1; $i <= 15; $i++) {
                    $fname = $firstNames[array_rand($firstNames)];
                    $lname = $lastNames[array_rand($lastNames)];
                    $gender = in_array($fname, ['Aisyah', 'Citra', 'Elsa', 'Gita', 'Indah', 'Kartika', 'Maya', 'Olivia', 'Qori', 'Sinta', 'Ulfa', 'Wulan', 'Zahra', 'Bella', 'Dian']) ? 'P' : 'L';
                    $nisn = '123' . str_pad($kelas->id_kelas, 3, '0', STR_PAD_LEFT) . str_pad($i, 2, '0', STR_PAD_LEFT);
                    Siswa::create([
                        'nisn' => $nisn,
                        'nama_siswa' => $fname . ' ' . $lname,
                        'jenis_kelamin' => $gender,
                        'id_kelas' => $kelas->id_kelas,
                        'is_aktif' => 1
                    ]);
                }
            }
        }
    }
}

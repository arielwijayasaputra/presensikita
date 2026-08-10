<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Siswa;

class JurnalSeeder extends Seeder
{
    public function run(): void
    {
        if (JurnalKelas::count() < 5) {
            $dates = ['2025-05-27', '2025-05-26', '2025-05-23', '2025-05-22', '2025-05-21'];
            $materis = [
                'Dasar-dasar Pemrograman & Alur Logika',
                'Praktikum Database Relasional & Query SQL',
                'Konsep Dasar Jaringan Komputer & IP Addressing',
                'Sistem Akuntansi Keuangan & Pembukuan Kas',
                'Desain Grafis & Pembuatan Layout Digital'
            ];

            foreach ($dates as $idx => $tgl) {
                $j = JurnalKelas::create([
                    'id_jadwal' => $idx + 1,
                    'tanggal' => $tgl,
                    'status_kehadiran_guru' => 'Hadir',
                    'materi' => $materis[$idx],
                    'jumlah_hadir' => rand(25, 32),
                    'waktu_input' => $tgl . ' 08:30:00'
                ]);

                $siswaSample = Siswa::take(3)->get();
                if (isset($siswaSample[0])) {
                    JurnalSiswaTidakHadir::create([
                        'id_jurnal' => $j->id_jurnal,
                        'id_siswa' => $siswaSample[0]->id_siswa,
                        'status' => 'S',
                        'keterangan' => 'Demam tinggi'
                    ]);
                }
                if (isset($siswaSample[1])) {
                    JurnalSiswaTidakHadir::create([
                        'id_jurnal' => $j->id_jurnal,
                        'id_siswa' => $siswaSample[1]->id_siswa,
                        'status' => 'I',
                        'keterangan' => 'Acara keluarga'
                    ]);
                }
            }
        }
    }
}

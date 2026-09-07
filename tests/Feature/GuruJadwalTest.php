<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Hari;
use App\Models\JadwalMengajar;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use Tests\TestCase;

class GuruJadwalTest extends TestCase
{
    public function test_guru_dashboard_renders_jadwal_mengajar_page()
    {
        $guru = Guru::where('is_admin', 0)->where('is_aktif', 1)->first();
        $this->assertNotNull($guru);

        $hariIni = Hari::getNamaHariFromAbbr(now()->format('D')) ?? 'Senin';
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X Test GuruJadwal'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );
        $mapel = Mapel::first() ?? Mapel::create(['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK', 'kelompok' => 'A']);
        $jam = JamPelajaran::firstOrCreate(
            ['hari' => $hariIni, 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );

        JadwalMengajar::firstOrCreate([
            'id_guru' => $guru->id_guru,
            'id_mapel' => $mapel->id_mapel,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahun->id_tahun_ajaran,
        ]);

        $response = $this->withSession([
            'auth_guru_id' => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ])->get(route('guru.index'));

        $response->assertStatus(200);
        $response->assertSee('id="page-jadwal-mengajar"', false);
        $response->assertSee('Jadwal Mengajar Hari Ini', false);
        $response->assertSee('id="nav-jadwal-mengajar"', false);
        $response->assertSee('Isi Jurnal', false);
    }
}

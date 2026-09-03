<?php

namespace Tests\Feature;

use App\Models\Guru;
use Tests\TestCase;

class GuruJadwalTest extends TestCase
{
    public function test_guru_dashboard_renders_jadwal_mengajar_page()
    {
        $guru = Guru::where('is_admin', 0)->where('is_aktif', 1)->first();
        $this->assertNotNull($guru);

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

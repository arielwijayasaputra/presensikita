<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Kelas;
use Tests\TestCase;

class StrukturalDashboardTest extends TestCase
{
    public function test_walikelas_can_access_dashboard_without_error()
    {
        $guru = Guru::where('is_admin', 0)->where('is_aktif', 1)->first();
        $this->assertNotNull($guru);

        $kelas = Kelas::first();
        $this->assertNotNull($kelas);

        $response = $this->withSession([
            'auth_guru_id' => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'walikelas',
            'auth_kelas_id' => $kelas->id_kelas,
            'auth_nama_kelas' => $kelas->nama_kelas,
        ])->get(route('walikelas.index', ['kelas_id' => $kelas->id_kelas]));

        $response->assertStatus(200);
    }
}

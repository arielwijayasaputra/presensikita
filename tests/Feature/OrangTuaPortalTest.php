<?php

namespace Tests\Feature;

use App\Models\Siswa;
use Tests\TestCase;

class OrangTuaPortalTest extends TestCase
{
    public function test_orang_tua_can_login_with_nisn_and_see_student_data()
    {
        $siswa = Siswa::with('kelas')->first();
        $this->assertNotNull($siswa, 'Data siswa harus ada di database.');

        // 1. Test login via NISN
        $response = $this->post(route('login.orangtua.post'), [
            'nisn' => $siswa->nisn,
        ]);

        $response->assertRedirect(route('orangtua.index'));
        $response->assertSessionHas('auth_siswa_id', $siswa->id_siswa);
        $response->assertSessionHas('auth_nisn', $siswa->nisn);
        $response->assertSessionHas('auth_role', 'orangtua');

        // 2. Test akses portal dashboard orang tua
        $dashboardResponse = $this->withSession([
            'auth_siswa_id' => $siswa->id_siswa,
            'auth_nisn' => $siswa->nisn,
            'auth_nama_siswa' => $siswa->nama_siswa,
            'auth_role' => 'orangtua',
        ])->get(route('orangtua.index'));

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee($siswa->nama_siswa, false);
        $dashboardResponse->assertSee($siswa->nisn, false);
        if ($siswa->kelas) {
            $dashboardResponse->assertSee($siswa->kelas->nama_kelas, false);
        }
    }
}

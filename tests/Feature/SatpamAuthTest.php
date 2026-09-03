<?php

namespace Tests\Feature;

use App\Models\AkunSatpam;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SatpamAuthTest extends TestCase
{
    public function test_satpam_can_login_successfully()
    {
        $satpam = AkunSatpam::first();
        $this->assertNotNull($satpam);

        $response = $this->post(route('login.peran.post'), [
            'username' => $satpam->username,
            'password' => 'satpam123',
            'peran' => 'Satpam',
            'role' => 'satpam',
        ]);

        $response->assertRedirect(route('satpam.index'));
        $response->assertSessionHas('auth_satpam_id', $satpam->id_satpam);
        $response->assertSessionHas('auth_role', 'satpam');
    }

    public function test_satpam_dashboard_accessible_when_authenticated()
    {
        $satpam = AkunSatpam::first();

        $response = $this->withSession([
            'auth_satpam_id' => $satpam->id_satpam,
            'auth_nama_guru' => $satpam->nama,
            'auth_is_admin' => 0,
            'auth_role' => 'satpam',
        ])->get(route('satpam.index'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Satpam', false);
        $response->assertSee('id="page-satpam-harian"', false);
        $response->assertSee('id="nav-satpam-harian"', false);
        $response->assertSee('Data Harian &amp; Riwayat Dispensasi', false);
    }

    public function test_satpam_update_profil()
    {
        $satpam = AkunSatpam::first();

        $response = $this->withSession([
            'auth_satpam_id' => $satpam->id_satpam,
            'auth_nama_guru' => $satpam->nama,
            'auth_is_admin' => 0,
            'auth_role' => 'satpam',
        ])->postJson(route('struktural.profil.update'), [
            'nama_guru' => 'Petugas Keamanan',
            'username' => 'satpam',
            'no_hp' => '089999999999',
            'current_password' => 'satpam123',
            'new_password' => 'satpam12345',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $satpam->refresh();
        $this->assertEquals('Petugas Keamanan', $satpam->nama);
        $this->assertTrue(Hash::check('satpam12345', $satpam->password_hash));

        // Restore back
        $satpam->update([
            'nama' => 'Satpam',
            'password_hash' => Hash::make('satpam123'),
            'no_hp' => null,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\AkunAdmin;
use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    public function test_admin_can_login_successfully()
    {
        $admin = AkunAdmin::first();
        $this->assertNotNull($admin);

        $response = $this->post(route('login'), [
            'username' => $admin->username,
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.index'));
        $response->assertSessionHas('auth_is_admin', 1);
        $response->assertSessionHas('auth_role', 'admin');
    }

    public function test_admin_dashboard_accessible_when_authenticated()
    {
        $admin = AkunAdmin::first();

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_guru_id' => $admin->id_admin,
            'auth_nama_admin' => $admin->nama,
            'auth_nama_guru' => $admin->nama,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->get(route('admin.index'));

        $response->assertStatus(200);
    }

    public function test_admin_update_profil_updates_timestamps()
    {
        $admin = AkunAdmin::first();

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_guru_id' => $admin->id_admin,
            'auth_nama_admin' => $admin->nama,
            'auth_nama_guru' => $admin->nama,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->postJson(route('profil.update'), [
            'nama_guru' => 'Administrator Presensi',
            'username' => 'admin_updated',
            'no_hp' => '081234567890',
            'current_password' => 'admin123',
            'new_password' => 'admin12345',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $admin->refresh();
        $this->assertEquals('admin_updated', $admin->username);
        $this->assertEquals('Administrator Presensi', $admin->nama);
        $this->assertEquals('081234567890', $admin->no_tlp);
        $this->assertEquals('081234567890', $admin->no_hp);
        $this->assertNotNull($admin->update_usn_at);
        $this->assertNotNull($admin->update_pw_at);
        $this->assertTrue(Hash::check('admin12345', $admin->password));

        // Restore back to original credentials for standard user
        $admin->update([
            'nama' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'password_hash' => Hash::make('admin123'),
            'no_tlp' => null,
            'no_hp' => null,
            'update_usn_at' => null,
            'update_pw_at' => null,
        ]);
    }
}

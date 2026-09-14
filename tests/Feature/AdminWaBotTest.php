<?php

namespace Tests\Feature;

use App\Models\AkunAdmin;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminWaBotTest extends TestCase
{
    public function test_admin_can_call_disconnect_wa_bot()
    {
        $admin = AkunAdmin::first() ?? AkunAdmin::create([
            'nama' => 'Admin Test',
            'username' => 'admin_test_' . uniqid(),
            'password_hash' => bcrypt('password'),
        ]);

        $response = $this->withSession([
            'auth_is_admin' => true,
            'auth_role' => 'admin',
            'auth_admin_id' => $admin->id_admin,
        ])->postJson(route('pengaturan.disconnect-wa'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_admin_can_check_status_and_qr_wa_bot()
    {
        $admin = AkunAdmin::first() ?? AkunAdmin::create([
            'nama' => 'Admin Test',
            'username' => 'admin_test_' . uniqid(),
            'password_hash' => bcrypt('password'),
        ]);

        $responseStatus = $this->withSession([
            'auth_is_admin' => true,
            'auth_role' => 'admin',
            'auth_admin_id' => $admin->id_admin,
        ])->getJson(route('pengaturan.status-wa'));

        $responseStatus->assertStatus(200);

        $responseQr = $this->withSession([
            'auth_is_admin' => true,
            'auth_role' => 'admin',
            'auth_admin_id' => $admin->id_admin,
        ])->getJson(route('pengaturan.qr-wa'));

        $responseQr->assertStatus(200);
    }
}

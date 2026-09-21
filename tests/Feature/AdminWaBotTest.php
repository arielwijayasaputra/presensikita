<?php

namespace Tests\Feature;

use App\Models\AkunAdmin;
use Tests\TestCase;

class AdminWaBotTest extends TestCase
{
    public function test_admin_can_call_disconnect_wa_bot()
    {
        $admin = AkunAdmin::first() ?? AkunAdmin::create([
            'nama' => 'Admin Test',
            'username' => 'admin_test_'.uniqid(),
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
            'username' => 'admin_test_'.uniqid(),
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

    public function test_notifikasi_popup_only_shows_within_one_day()
    {
        $guru = \App\Models\Guru::first();
        $uniq = uniqid();

        // Notifikasi baru (hari ini / 2 jam lalu)
        $idBaru = \DB::table('notifikasi')->insertGetId([
            'id_guru' => null,
            'id_kelas' => null,
            'judul' => 'Notif Baru ' . $uniq,
            'pesan' => 'Pesan baru',
            'tipe' => 'info',
            'is_read' => 0,
            'created_at' => now()->subHours(2),
        ]);

        // Notifikasi lama (lebih dari 1 hari lalu)
        $idLama = \DB::table('notifikasi')->insertGetId([
            'id_guru' => null,
            'id_kelas' => null,
            'judul' => 'Notif Lama ' . $uniq,
            'pesan' => 'Pesan lama',
            'tipe' => 'info',
            'is_read' => 0,
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->withSession([
            'auth_guru_id' => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->getJson(route('notifikasi.index'));

        $response->assertStatus(200);
        $notifList = collect($response->json('notifikasi'));

        // Harus ada notifikasi baru
        $this->assertTrue($notifList->contains('id', $idBaru));

        // Tidak boleh ada notifikasi yang lebih dari 1 hari
        $this->assertFalse($notifList->contains('id', $idLama));

        // Cleanup test data
        \DB::table('notifikasi')->whereIn('id', [$idBaru, $idLama])->delete();
    }
}

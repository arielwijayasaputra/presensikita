<?php

namespace Tests\Feature;

use App\Models\AkunAdmin;
use App\Models\DispenSiswa;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\IzinGuru;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengaturan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppBotTest extends TestCase
{
    private function authenticateAdmin(): AkunAdmin
    {
        return AkunAdmin::first() ?? AkunAdmin::create([
            'nama' => 'Admin WA Test',
            'username' => 'adminwatest',
            'password_hash' => bcrypt('password'),
            'is_aktif' => 1,
        ]);
    }

    public function test_normalize_phone_number()
    {
        $this->assertEquals('628123456789', WhatsAppService::normalizePhoneNumber('08123456789'));
        $this->assertEquals('628123456789', WhatsAppService::normalizePhoneNumber('+628123456789'));
        $this->assertEquals('628123456789', WhatsAppService::normalizePhoneNumber('62812-3456-789'));
        $this->assertEquals('628123456789', WhatsAppService::normalizePhoneNumber('0812 3456 789'));
        $this->assertNull(WhatsAppService::normalizePhoneNumber(''));
        $this->assertNull(WhatsAppService::normalizePhoneNumber(null));
    }

    public function test_generate_wame_link()
    {
        $link = WhatsAppService::generateWaMeLink('08123456789', 'Halo Tes');
        $this->assertStringContainsString('https://wa.me/628123456789?text=Halo%20Tes', $link);
    }

    public function test_check_bot_status_live_or_fake()
    {
        Http::fake([
            'http://127.0.0.1:3000/status' => Http::response(['status' => 'connected'], 200),
        ]);

        $status = WhatsAppService::checkBotStatus();
        $this->assertTrue($status['online']);
        $this->assertEquals('connected', $status['status']);
    }

    public function test_kirim_pesan_via_whatsapp_service()
    {
        Http::fake([
            'http://127.0.0.1:3000/send-message' => Http::response([
                'status' => true,
                'message' => 'Pesan berhasil dikirim',
            ], 200),
        ]);

        $res = WhatsAppService::kirimPesan('08123456789', 'Pesan uji coba');
        $this->assertTrue($res['success']);
        $this->assertEquals('Pesan berhasil dikirim', $res['message']);
    }

    public function test_admin_can_update_wa_settings_and_test_send()
    {
        $admin = $this->authenticateAdmin();

        $updateResponse = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->postJson('/pengaturan/update', [
            'nama_sekolah' => 'SMKN 1 Boyolangu',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'wa_gateway_aktif' => '1',
            'wa_gateway_endpoint' => 'http://127.0.0.1:3000/send-message',
            'wa_nomor_waka_kesiswaan' => '0811111111',
            'wa_nomor_waka_sdm' => '0822222222',
            'wa_nomor_kepsek' => '0833333333',
        ]);

        $updateResponse->assertStatus(200);
        $this->assertEquals('0811111111', Pengaturan::get('wa_nomor_waka_kesiswaan'));
        $this->assertEquals('0822222222', Pengaturan::get('wa_nomor_waka_sdm'));
        $this->assertEquals('0833333333', Pengaturan::get('wa_nomor_kepsek'));

        Http::fake([
            'http://127.0.0.1:3000/send-message' => Http::response([
                'status' => true,
                'message' => 'Pesan berhasil dikirim',
            ], 200),
        ]);

        $testResponse = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->postJson('/pengaturan/test-wa', [
            'target_phone' => '081234567890',
            'pesan' => 'Tes pesan dari admin',
        ]);

        $testResponse->assertStatus(200);
        $testResponse->assertJson(['status' => 'success']);
    }

    public function test_admin_can_update_wa_via_dedicated_endpoint()
    {
        $admin = $this->authenticateAdmin();

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->postJson('/pengaturan/update-wa', [
            'wa_gateway_aktif' => true,
            'wa_nomor_waka_kesiswaan' => '0899999999',
            'wa_nomor_waka_sdm' => '0888888888',
            'wa_nomor_kepsek' => '0877777777',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertEquals('0899999999', Pengaturan::get('wa_nomor_waka_kesiswaan'));
        $this->assertEquals('0888888888', Pengaturan::get('wa_nomor_waka_sdm'));
        $this->assertEquals('0877777777', Pengaturan::get('wa_nomor_kepsek'));
        $this->assertEquals('1', Pengaturan::get('wa_gateway_aktif'));
    }
}

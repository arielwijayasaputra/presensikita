<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\GuruPiket;
use App\Models\IzinGuru;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class IzinGuruPiketWorkflowTest extends TestCase
{
    protected function tearDown(): void
    {
        IzinGuru::where('alasan', 'like', 'TEST_WORKFLOW%')->forceDelete();
        GuruPiket::whereDate('tanggal', now()->toDateString())->where('id_guru', 99999)->delete();
        parent::tearDown();
    }

    public function test_pengajuan_izin_guru_pengajar_masuk_antrean_menunggu_piket_dan_tidak_kirim_wa_langsung()
    {
        Http::fake();

        $guruPengajar = Guru::where('is_admin', 0)->where('is_aktif', 1)->firstOrFail();

        $response = $this->withSession([
            'auth_guru_id' => $guruPengajar->id_guru,
            'auth_nama_guru' => $guruPengajar->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ])->postJson(route('guru.izin-guru.store'), [
            'tanggal_izin' => now()->toDateString(),
            'alasan' => 'TEST_WORKFLOW: Guru pengajar sakit',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'menunggu_piket' => true,
        ]);

        $izin = IzinGuru::where('alasan', 'TEST_WORKFLOW: Guru pengajar sakit')->first();
        $this->assertNotNull($izin);
        $this->assertEquals('menunggu', $izin->status_konfirmasi_piket);
        $this->assertNull($izin->id_guru_piket);
        $this->assertNull($izin->dikonfirmasi_piket_pada);
        $this->assertFalse($izin->isDikonfirmasiPiket());

        // HTTP request WA bot tidak boleh dipanggil saat pengajuan guru
        Http::assertNothingSent();

        // Approval link public harus 403 saat belum dikonfirmasi piket
        $approvalUrl = URL::temporarySignedRoute('izin-guru.public.role', now()->addDays(2), [
            'izin' => $izin->id_izin_guru,
            'role' => 'kepsek',
        ], false);
        $publicResponse = $this->get($approvalUrl);
        $publicResponse->assertStatus(403);
    }

    public function test_non_piket_tidak_bisa_konfirmasi_izin()
    {
        $gurus = Guru::where('is_admin', 0)->where('is_aktif', 1)->take(2)->get();
        $guru1 = $gurus[0];
        $guru2 = $gurus[1];

        // Pastikan guru2 bukan guru piket hari ini
        GuruPiket::where('id_guru', $guru2->id_guru)->whereDate('tanggal', now()->toDateString())->forceDelete();

        $izin = IzinGuru::create([
            'id_guru' => $guru1->id_guru,
            'tanggal_izin' => now()->toDateString(),
            'alasan' => 'TEST_WORKFLOW: Menunggu piket',
            'status_konfirmasi_piket' => 'menunggu',
        ]);

        $response = $this->withSession([
            'auth_guru_id' => $guru2->id_guru,
            'auth_nama_guru' => $guru2->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ])->postJson(route('guru.izin-guru.konfirmasi-piket', ['izin' => $izin->id_izin_guru]), [
            'catatan_piket' => 'Catatan coba konfirmasi',
        ]);

        $response->assertStatus(403);
    }

    public function test_guru_piket_bisa_konfirmasi_izin_dan_meneruskan_ke_wa_kepsek_dan_waka()
    {
        Http::fake();

        $gurus = Guru::where('is_admin', 0)->where('is_aktif', 1)->take(2)->get();
        $guruPengajar = $gurus[0];
        $guruPiket = $gurus[1];

        // Daftarkan sebagai guru piket hari ini
        GuruPiket::updateOrCreate(
            ['id_guru' => $guruPiket->id_guru, 'tanggal' => now()->toDateString()],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $izin = IzinGuru::create([
            'id_guru' => $guruPengajar->id_guru,
            'tanggal_izin' => now()->toDateString(),
            'alasan' => 'TEST_WORKFLOW: Izin untuk dikonfirmasi piket',
            'status_konfirmasi_piket' => 'menunggu',
        ]);

        $response = $this->withSession([
            'auth_guru_id' => $guruPiket->id_guru,
            'auth_nama_guru' => $guruPiket->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru_piket',
        ])->postJson(route('izin-guru.konfirmasi-piket', ['izin' => $izin->id_izin_guru]), [
            'catatan_piket' => 'Sudah dikonfirmasi piket, tugas siswa sudah diberikan.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $izin->refresh();
        $this->assertEquals('dikonfirmasi', $izin->status_konfirmasi_piket);
        $this->assertEquals($guruPiket->id_guru, $izin->id_guru_piket);
        $this->assertNotNull($izin->dikonfirmasi_piket_pada);
        $this->assertEquals('Sudah dikonfirmasi piket, tugas siswa sudah diberikan.', $izin->catatan_piket);
        $this->assertTrue($izin->isDikonfirmasiPiket());

        // Setelah dikonfirmasi piket, halaman persetujuan dapat dibuka
        $approvalUrl = URL::temporarySignedRoute('izin-guru.public.role', now()->addDays(2), [
            'izin' => $izin->id_izin_guru,
            'role' => 'kepsek',
        ], false);
        $publicResponse = $this->get($approvalUrl);
        $publicResponse->assertStatus(200);
    }

    public function test_input_langsung_oleh_guru_piket_otomatis_berstatus_dikonfirmasi()
    {
        Http::fake();

        $gurus = Guru::where('is_admin', 0)->where('is_aktif', 1)->take(2)->get();
        $guruPengajar = $gurus[0];
        $guruPiket = $gurus[1];

        GuruPiket::updateOrCreate(
            ['id_guru' => $guruPiket->id_guru, 'tanggal' => now()->toDateString()],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $response = $this->withSession([
            'auth_guru_id' => $guruPiket->id_guru,
            'auth_nama_guru' => $guruPiket->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru_piket',
        ])->postJson(route('izin-guru.store'), [
            'id_guru' => $guruPengajar->id_guru,
            'tanggal_izin' => now()->toDateString(),
            'alasan' => 'TEST_WORKFLOW: Input langsung piket',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $izin = IzinGuru::where('alasan', 'TEST_WORKFLOW: Input langsung piket')->first();
        $this->assertNotNull($izin);
        $this->assertEquals('dikonfirmasi', $izin->status_konfirmasi_piket);
        $this->assertEquals($guruPiket->id_guru, $izin->id_guru_piket);
        $this->assertNotNull($izin->dikonfirmasi_piket_pada);
        $this->assertTrue($izin->isDikonfirmasiPiket());
    }

    public function test_izin_menunggu_piket_tidak_muncul_di_waka_sdm_tetapi_muncul_setelah_dikonfirmasi()
    {
        $guru = Guru::where('is_admin', 0)->where('is_aktif', 1)->firstOrFail();
        $wakaSdm = \App\Models\AkunWakaSdm::first();
        if (! $wakaSdm) {
            $wakaSdm = \App\Models\AkunWakaSdm::create([
                'nama' => 'Waka SDM Test',
                'username' => 'wakasdm_test',
                'password_hash' => bcrypt('password'),
                'is_aktif' => 1,
            ]);
        }

        $izin = IzinGuru::create([
            'id_guru' => $guru->id_guru,
            'tanggal_izin' => now()->toDateString(),
            'alasan' => 'TEST_WORKFLOW: Belum konfirmasi piket',
            'status_konfirmasi_piket' => 'menunggu',
        ]);

        $session = [
            'auth_role' => 'waka_sdm',
            'auth_waka_sdm_id' => $wakaSdm->id_waka_sdm,
            'auth_nama' => $wakaSdm->nama,
        ];

        $response = $this->withSession($session)->get(route('wakasdm.index'));
        $response->assertStatus(200);
        $response->assertDontSee('TEST_WORKFLOW: Belum konfirmasi piket');

        // Setelah dikonfirmasi piket:
        $izin->update([
            'status_konfirmasi_piket' => 'dikonfirmasi',
            'id_guru_piket' => $guru->id_guru,
            'dikonfirmasi_piket_pada' => now(),
        ]);

        $responseConfirmed = $this->withSession($session)->get(route('wakasdm.index'));
        $responseConfirmed->assertStatus(200);
        $responseConfirmed->assertSee('TEST_WORKFLOW: Belum konfirmasi piket');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Hari;
use App\Models\JadwalMengajar;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengaturan;
use App\Models\TahunAjaran;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalMajuTest extends TestCase
{
    private function loginAsAdmin()
    {
        $admin = \App\Models\AkunAdmin::first() ?? \App\Models\AkunAdmin::create([
            'nama' => 'Admin Test',
            'username' => 'admintest_maju',
            'password_hash' => bcrypt('password'),
            'is_aktif' => 1,
        ]);

        return $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ]);
    }

    public function test_admin_can_toggle_kemajuan_jadwal_senin()
    {
        $adminTest = $this->loginAsAdmin();

        $response = $adminTest->postJson(route('jam-pelajaran.toggle-kemajuan'), [
            'hari' => 'Senin',
            'status' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'hari' => 'Senin',
            'is_aktif' => true,
        ]);

        $this->assertEquals('1', Pengaturan::get('jadwal_maju_senin'));
        $this->assertTrue(JadwalService::isJadwalMaju('Senin'));

        // Nonaktifkan
        $responseDeactivate = $adminTest->postJson(route('jam-pelajaran.toggle-kemajuan'), [
            'hari' => 'Senin',
            'status' => false,
        ]);

        $responseDeactivate->assertStatus(200);
        $responseDeactivate->assertJson([
            'status' => 'success',
            'hari' => 'Senin',
            'is_aktif' => false,
        ]);

        $this->assertEquals('0', Pengaturan::get('jadwal_maju_senin'));
        $this->assertFalse(JadwalService::isJadwalMaju('Senin'));
    }

    public function test_admin_can_toggle_kemajuan_jadwal_jumat()
    {
        $adminTest = $this->loginAsAdmin();

        $response = $adminTest->postJson(route('jam-pelajaran.toggle-kemajuan'), [
            'hari' => 'Jumat',
            'status' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'hari' => 'Jumat',
            'is_aktif' => true,
        ]);

        $this->assertEquals('1', Pengaturan::get('jadwal_maju_jumat'));
        $this->assertTrue(JadwalService::isJadwalMaju('Jumat'));
    }

    public function test_toggle_validation_rejects_invalid_input()
    {
        $adminTest = $this->loginAsAdmin();

        // Hari selain Senin dan Jumat ditolak
        $response = $adminTest->postJson(route('jam-pelajaran.toggle-kemajuan'), [
            'hari' => 'Selasa',
            'status' => true,
        ]);
        $response->assertStatus(422);

        // Status wajib boolean
        $responseNoStatus = $adminTest->postJson(route('jam-pelajaran.toggle-kemajuan'), [
            'hari' => 'Senin',
        ]);
        $responseNoStatus->assertStatus(422);
    }

    public function test_jadwal_service_shifts_senin_schedule_when_active()
    {
        $jam1 = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );
        $jam2 = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 2],
            ['jam_mulai' => '07:40:00', 'jam_selesai' => '08:20:00']
        );
        $jam3 = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 3],
            ['jam_mulai' => '08:20:00', 'jam_selesai' => '09:00:00']
        );

        $upacara = (object) [
            'id_jadwal' => 101,
            'id_kelas' => 1,
            'id_jam' => $jam1->id_jam,
            'jam_ke' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:40:00',
            'nama_mapel' => 'Upacara/Apel',
        ];
        $matematika = (object) [
            'id_jadwal' => 102,
            'id_kelas' => 1,
            'id_jam' => $jam2->id_jam,
            'jam_ke' => 2,
            'jam_mulai' => '07:40:00',
            'jam_selesai' => '08:20:00',
            'nama_mapel' => 'Matematika',
        ];
        $inggris = (object) [
            'id_jadwal' => 103,
            'id_kelas' => 1,
            'id_jam' => $jam3->id_jam,
            'jam_ke' => 3,
            'jam_mulai' => '08:20:00',
            'jam_selesai' => '09:00:00',
            'nama_mapel' => 'Bahasa Inggris',
        ];

        $jadwalCollection = collect([$upacara, $matematika, $inggris]);

        // Saat NONAKTIF (normal):
        Pengaturan::set('jadwal_maju_senin', '0');
        $normal = JadwalService::applyJadwalMaju($jadwalCollection, 'Senin');
        $this->assertCount(3, $normal);
        $this->assertEquals(1, $normal[0]->jam_ke);
        $this->assertEquals('Upacara/Apel', $normal[0]->nama_mapel);
        $this->assertEquals(2, $normal[1]->jam_ke);
        $this->assertEquals('Matematika', $normal[1]->nama_mapel);

        // Saat AKTIF (maju):
        Pengaturan::set('jadwal_maju_senin', '1');
        $shifted = JadwalService::applyJadwalMaju($jadwalCollection, 'Senin');
        $this->assertCount(2, $shifted);
        // Matematika maju ke Jam ke-1 (07:00 - 07:40)
        $this->assertEquals(1, $shifted[0]->jam_ke);
        $this->assertEquals($jam1->id_jam, $shifted[0]->id_jam);
        $this->assertEquals('07:00:00', $shifted[0]->jam_mulai);
        $this->assertEquals('07:40:00', $shifted[0]->jam_selesai);
        $this->assertEquals('Matematika', $shifted[0]->nama_mapel);

        // Bahasa Inggris maju ke Jam ke-2 (07:40 - 08:20)
        $this->assertEquals(2, $shifted[1]->jam_ke);
        $this->assertEquals($jam2->id_jam, $shifted[1]->id_jam);
        $this->assertEquals('07:40:00', $shifted[1]->jam_mulai);
        $this->assertEquals('08:20:00', $shifted[1]->jam_selesai);
        $this->assertEquals('Bahasa Inggris', $shifted[1]->nama_mapel);
    }

    public function test_jadwal_service_shifts_jumat_schedule_when_active()
    {
        $jam101 = JamPelajaran::firstOrCreate(
            ['hari' => 'Jumat', 'jam_ke' => 101],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:30:00']
        );
        $jam102 = JamPelajaran::firstOrCreate(
            ['hari' => 'Jumat', 'jam_ke' => 102],
            ['jam_mulai' => '07:30:00', 'jam_selesai' => '08:00:00']
        );
        $jam103 = JamPelajaran::firstOrCreate(
            ['hari' => 'Jumat', 'jam_ke' => 103],
            ['jam_mulai' => '08:00:00', 'jam_selesai' => '08:30:00']
        );

        $pembiasaan = (object) [
            'id_jadwal' => 201,
            'id_kelas' => 1,
            'id_jam' => $jam101->id_jam,
            'jam_ke' => 101,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:30:00',
            'nama_mapel' => 'Pembiasaan Hari Jumat',
        ];
        $koding = (object) [
            'id_jadwal' => 202,
            'id_kelas' => 1,
            'id_jam' => $jam102->id_jam,
            'jam_ke' => 102,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '08:00:00',
            'nama_mapel' => 'Koding',
        ];
        $pai = (object) [
            'id_jadwal' => 203,
            'id_kelas' => 1,
            'id_jam' => $jam103->id_jam,
            'jam_ke' => 103,
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '08:30:00',
            'nama_mapel' => 'PAI',
        ];

        $jadwalCollection = collect([$pembiasaan, $koding, $pai]);

        // Saat NONAKTIF:
        Pengaturan::set('jadwal_maju_jumat', '0');
        $normal = JadwalService::applyJadwalMaju($jadwalCollection, 'Jumat');
        $this->assertCount(3, $normal);
        $this->assertEquals(101, $normal[0]->jam_ke);
        $this->assertEquals('Pembiasaan Hari Jumat', $normal[0]->nama_mapel);

        // Saat AKTIF:
        Pengaturan::set('jadwal_maju_jumat', '1');
        $shifted = JadwalService::applyJadwalMaju($jadwalCollection, 'Jumat');
        $this->assertCount(2, $shifted);
        // Koding maju ke Jam ke-101 (07:00 - 07:30)
        $this->assertEquals(101, $shifted[0]->jam_ke);
        $this->assertEquals($jam101->id_jam, $shifted[0]->id_jam);
        $this->assertEquals('07:00:00', $shifted[0]->jam_mulai);
        $this->assertEquals('07:30:00', $shifted[0]->jam_selesai);
        $this->assertEquals('Koding', $shifted[0]->nama_mapel);

        // PAI maju ke Jam ke-102 (07:30 - 08:00)
        $this->assertEquals(102, $shifted[1]->jam_ke);
        $this->assertEquals($jam102->id_jam, $shifted[1]->id_jam);
        $this->assertEquals('07:30:00', $shifted[1]->jam_mulai);
        $this->assertEquals('08:00:00', $shifted[1]->jam_selesai);
        $this->assertEquals('PAI', $shifted[1]->nama_mapel);
    }

    public function test_jadwal_aktif_api_returns_shifted_schedule_for_guru()
    {
        $guru = Guru::where('is_admin', 0)->where('is_aktif', 1)->first();
        $this->assertNotNull($guru);

        $hariIni = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray()[now()->format('l')] ?? now()->format('l');

        if ($hariIni === 'Senin') {
            Pengaturan::set('jadwal_maju_senin', '1');
        } elseif ($hariIni === 'Jumat') {
            Pengaturan::set('jadwal_maju_jumat', '1');
        }

        $response = $this->withSession([
            'auth_guru_id' => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ])->getJson(route('absensi.jadwal-aktif'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'active_kelas_ids',
            'jadwal_per_kelas',
            'kelas_hari_ini',
        ]);
    }
}

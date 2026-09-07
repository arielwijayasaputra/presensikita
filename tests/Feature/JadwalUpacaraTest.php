<?php

namespace Tests\Feature;

use App\Models\AkunAdmin;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\JadwalMengajar;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class JadwalUpacaraTest extends TestCase
{
    private function authenticateAdmin(): AkunAdmin
    {
        return AkunAdmin::first() ?? AkunAdmin::create([
            'nama' => 'Admin Test',
            'username' => 'admintest',
            'password_hash' => bcrypt('password'),
            'is_aktif' => 1,
        ]);
    }

    public function test_import_jadwal_upacara_always_sets_id_guru_to_null_even_if_guru_provided()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X Test Upacara Import'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );

        $guru = Guru::firstOrCreate(
            ['username' => 'guru_upacara_test'],
            ['nama_guru' => 'Guru Upacara Test, S.Pd', 'Peran' => 'Guru', 'password_hash' => bcrypt('password'), 'is_aktif' => 1]
        );

        $jam = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );

        $mapelUpacara = Mapel::firstOrCreate(
            ['nama_mapel' => 'Upacara/Apel'],
            ['kode_mapel' => 'UPACARA', 'kelompok' => 'A']
        );

        $csvContent = implode("\n", [
            'Hari,Kelas,Jam,MataPelajaran,Guru',
            'Senin,X Test Upacara Import,1,Upacara/Apel,"Guru Upacara Test, S.Pd"',
        ]);
        $file = UploadedFile::fake()->createWithContent('jadwal_upacara.csv', $csvContent);

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->post(route('jadwal.import'), ['file_jadwal' => $file]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Senin',
            'id_jam' => $jam->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapelUpacara->id_mapel,
            'id_guru' => null,
        ]);
    }

    public function test_store_jadwal_upacara_nullifies_guru()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X Test Upacara Store'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );

        $guru = Guru::firstOrCreate(
            ['username' => 'guru_store_test'],
            ['nama_guru' => 'Guru Store Test, S.Pd', 'Peran' => 'Guru', 'password_hash' => bcrypt('password'), 'is_aktif' => 1]
        );

        $jam = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );

        $mapelUpacara = Mapel::firstOrCreate(
            ['nama_mapel' => 'Upacara/Apel'],
            ['kode_mapel' => 'UPACARA', 'kelompok' => 'A']
        );

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->post(route('jadwal.tambah'), [
            'hari' => 'Senin',
            'id_jam' => $jam->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapelUpacara->id_mapel,
            'id_guru' => $guru->id_guru,
            'id_tahun_ajaran' => $tahun->id_tahun_ajaran,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Senin',
            'id_jam' => $jam->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapelUpacara->id_mapel,
            'id_guru' => null,
        ]);
    }

    public function test_update_jadwal_upacara_nullifies_guru()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X Test Upacara Update'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );

        $guru = Guru::firstOrCreate(
            ['username' => 'guru_update_test'],
            ['nama_guru' => 'Guru Update Test, S.Pd', 'Peran' => 'Guru', 'password_hash' => bcrypt('password'), 'is_aktif' => 1]
        );

        $jam = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );

        $mapelUpacara = Mapel::firstOrCreate(
            ['nama_mapel' => 'Upacara/Apel'],
            ['kode_mapel' => 'UPACARA', 'kelompok' => 'A']
        );

        $jadwal = JadwalMengajar::withTrashed()->updateOrCreate(
            ['hari' => 'Senin', 'id_jam' => $jam->id_jam, 'id_kelas' => $kelas->id_kelas, 'id_tahun_ajaran' => $tahun->id_tahun_ajaran],
            ['id_mapel' => $mapelUpacara->id_mapel, 'id_guru' => null]
        );
        if ($jadwal->trashed()) {
            $jadwal->restore();
        }

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->post(route('jadwal.update', $jadwal->id_jadwal), [
            'hari' => 'Senin',
            'id_jam' => $jam->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapelUpacara->id_mapel,
            'id_guru' => $guru->id_guru,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('jadwal_mengajar', [
            'id_jadwal' => $jadwal->id_jadwal,
            'id_guru' => null,
        ]);
    }

    public function test_cannot_tugaskan_guru_for_upacara_schedule()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X Test Upacara Tugaskan'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );

        $guru = Guru::firstOrCreate(
            ['username' => 'guru_tugaskan_test'],
            ['nama_guru' => 'Guru Tugaskan Test, S.Pd', 'Peran' => 'Guru', 'password_hash' => bcrypt('password'), 'is_aktif' => 1]
        );

        $jam = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );

        $mapelUpacara = Mapel::firstOrCreate(
            ['nama_mapel' => 'Upacara/Apel'],
            ['kode_mapel' => 'UPACARA', 'kelompok' => 'A']
        );

        $jadwal = JadwalMengajar::withTrashed()->updateOrCreate(
            ['hari' => 'Senin', 'id_jam' => $jam->id_jam, 'id_kelas' => $kelas->id_kelas, 'id_tahun_ajaran' => $tahun->id_tahun_ajaran],
            ['id_mapel' => $mapelUpacara->id_mapel, 'id_guru' => null]
        );
        if ($jadwal->trashed()) {
            $jadwal->restore();
        }

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->post(route('jadwal.tugaskan-guru', $jadwal->id_jadwal), [
            'id_guru' => $guru->id_guru,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Jadwal upacara tidak memerlukan guru pengajar.',
        ]);

        $this->assertDatabaseHas('jadwal_mengajar', [
            'id_jadwal' => $jadwal->id_jadwal,
            'id_guru' => null,
        ]);
    }

    public function test_jadwal_page_displays_tidak_ada_guru_for_upacara()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X Test Upacara Display'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );

        $jam = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );

        $mapelUpacara = Mapel::firstOrCreate(
            ['nama_mapel' => 'Upacara/Apel'],
            ['kode_mapel' => 'UPACARA', 'kelompok' => 'A']
        );

        $jadwal = JadwalMengajar::withTrashed()->updateOrCreate(
            ['hari' => 'Senin', 'id_jam' => $jam->id_jam, 'id_kelas' => $kelas->id_kelas, 'id_tahun_ajaran' => $tahun->id_tahun_ajaran],
            ['id_mapel' => $mapelUpacara->id_mapel, 'id_guru' => null]
        );
        if ($jadwal->trashed()) {
            $jadwal->restore();
        }

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->get(route('admin.index'));

        $response->assertStatus(200);
        $response->assertSee('Tidak Ada Guru', false);
    }
}

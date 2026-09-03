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

class JadwalImportTest extends TestCase
{
    private function authenticateAdmin(): AkunAdmin
    {
        $admin = AkunAdmin::first() ?? AkunAdmin::create([
            'nama' => 'Admin Test',
            'username' => 'admintest',
            'password_hash' => bcrypt('password'),
            'is_aktif' => 1,
        ]);

        return $admin;
    }

    public function test_import_jadwal_with_smkn1_boyolangu_csv_structure()
    {
        $admin = $this->authenticateAdmin();

        $tahun = TahunAjaran::firstOrCreate(
            ['is_aktif' => 1],
            ['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil']
        );

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X TKI Import Test'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );

        $guru = Guru::firstOrCreate(
            ['nama_guru' => "Muto'atul Khosi'ah Import Test, S.Pd"],
            ['Peran' => 'Guru', 'username' => 'mutoatul_import_test', 'password_hash' => bcrypt('password'), 'is_aktif' => 1]
        );

        // Siapkan master jam pelajaran untuk Senin (jam 1, 2, 3) dan Jumat (jam 101, 102, 103, 104)
        JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );
        JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 2],
            ['jam_mulai' => '07:40:00', 'jam_selesai' => '08:20:00']
        );
        JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 3],
            ['jam_mulai' => '08:20:00', 'jam_selesai' => '09:00:00']
        );
        JamPelajaran::firstOrCreate(
            ['hari' => 'Jumat', 'jam_ke' => 101],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:30:00']
        );
        JamPelajaran::firstOrCreate(
            ['hari' => 'Jumat', 'jam_ke' => 102],
            ['jam_mulai' => '07:30:00', 'jam_selesai' => '08:00:00']
        );
        JamPelajaran::firstOrCreate(
            ['hari' => 'Jumat', 'jam_ke' => 103],
            ['jam_mulai' => '08:00:00', 'jam_selesai' => '08:30:00']
        );
        JamPelajaran::firstOrCreate(
            ['hari' => 'Jumat', 'jam_ke' => 104],
            ['jam_mulai' => '08:30:00', 'jam_selesai' => '09:00:00']
        );

        $csvContent = implode("\n", [
            'Kelas,Hari,Jam,WaktuMulai,WaktuSelesai,Kind,MataPelajaran,Guru,Ruang',
            'X TKI Import Test,Senin,1,07:00,07:40,UPACARA,Upacara/Apel,,',
            'X TKI Import Test,Senin,2-3,07:40,09:00,CLASS,Bahasa Inggris,"Muto\'atul Khosi\'ah Import Test, S.Pd",Lab. KI 1',
            'X TKI Import Test,Jumat,1,07:00,07:30,PEMBIASAAN,Pembiasaan Hari Jumat,,',
            'X TKI Import Test,Jumat,2-3-4,07:30,09:00,CLASS,Bahasa Inggris,"Muto\'atul Khosi\'ah Import Test, S.Pd",Lab. KI 1',
        ]);

        $file = UploadedFile::fake()->createWithContent('jadwal_test.csv', $csvContent);

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->post(route('jadwal.import'), [
            'file_jadwal' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        // Jam ke-1 Senin (Upacara/Apel) tanpa guru
        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Senin',
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => null,
        ]);

        // Jam ke-2 & ke-3 Senin (Bahasa Inggris) dengan guru
        $seninJam2 = JamPelajaran::where('hari', 'Senin')->where('jam_ke', 2)->first();
        $seninJam3 = JamPelajaran::where('hari', 'Senin')->where('jam_ke', 3)->first();

        $this->assertNotNull($seninJam2);
        $this->assertNotNull($seninJam3);

        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Senin',
            'id_jam' => $seninJam2->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru->id_guru,
        ]);

        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Senin',
            'id_jam' => $seninJam3->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru->id_guru,
        ]);

        // Jam ke-1 Jumat (Pembiasaan Hari Jumat -> jam_ke 101)
        $jumatJam1 = JamPelajaran::where('hari', 'Jumat')->where('jam_ke', 101)->first();
        $this->assertNotNull($jumatJam1);

        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Jumat',
            'id_jam' => $jumatJam1->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => null,
        ]);

        // Jam ke-2, 3, 4 Jumat (Bahasa Inggris -> jam_ke 102, 103, 104)
        $jumatJam2 = JamPelajaran::where('hari', 'Jumat')->where('jam_ke', 102)->first();
        $jumatJam3 = JamPelajaran::where('hari', 'Jumat')->where('jam_ke', 103)->first();
        $jumatJam4 = JamPelajaran::where('hari', 'Jumat')->where('jam_ke', 104)->first();

        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Jumat',
            'id_jam' => $jumatJam2->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru->id_guru,
        ]);
        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Jumat',
            'id_jam' => $jumatJam3->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru->id_guru,
        ]);
        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Jumat',
            'id_jam' => $jumatJam4->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru->id_guru,
        ]);
    }

    public function test_import_jadwal_only_uses_required_columns_and_ignores_other_columns()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $this->assertNotNull($tahun, 'Data tahun ajaran harus ada di database.');

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X TKI Import Test'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );
        $guru = Guru::firstOrCreate(
            ['nama_guru' => "Muto'atul Khosi'ah Import Test, S.Pd"],
            ['Peran' => 'Guru', 'username' => 'mutoatul_import_test', 'password_hash' => bcrypt('password'), 'is_aktif' => 1]
        );
        $jam = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );

        $csvContent = implode("\n", [
            'Hari,Kelas,Jam,MataPelajaran,Guru,Kind,Ruang,Catatan',
            "Senin,X TKI Import Test,1,Bahasa Inggris,\"Muto'atul Khosi'ah Import Test, S.Pd\",KOLOM_DIABAIKAN,Ruang_TIDAK_DIPAKAI,Nilai_TIDAK_DIPAKAI",
        ]);
        $file = UploadedFile::fake()->createWithContent('jadwal_required_columns.csv', $csvContent);

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->post(route('jadwal.import'), [
            'file_jadwal' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'total_rows' => 1,
        ]);

        $mapel = Mapel::where('nama_mapel', 'Bahasa Inggris')->first();
        $this->assertNotNull($mapel);
        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Senin',
            'id_jam' => $jam->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id_mapel,
            'id_guru' => $guru->id_guru,
        ]);
    }

    public function test_import_accepts_mata_pelajaram_header_typo()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X TKI Header Typo Test'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );
        $jam = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 2],
            ['jam_mulai' => '07:40:00', 'jam_selesai' => '08:20:00']
        );

        $csvContent = "\xEF\xBB\xBFHari,Kelas,Jam,Mata Pelajaram,Guru\nSenin,X TKI Header Typo Test,2,Bahasa Inggris,";
        $file = UploadedFile::fake()->createWithContent('jadwal_header_typo.csv', $csvContent);

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->post(route('jadwal.import'), ['file_jadwal' => $file]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'total_rows' => 1]);
        $this->assertDatabaseHas('jadwal_mengajar', [
            'hari' => 'Senin',
            'id_jam' => $jam->id_jam,
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => null,
        ]);
    }

    public function test_can_soft_delete_all_active_jadwal()
    {
        $admin = $this->authenticateAdmin();
        $jadwal = JadwalMengajar::whereNull('deleted_at')->first();
        $this->assertNotNull($jadwal, 'Data jadwal aktif harus ada di database.');

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->delete(route('jadwal.hapus-semua'));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertSoftDeleted('jadwal_mengajar', ['id_jadwal' => $jadwal->id_jadwal]);
        $this->assertDatabaseHas('jadwal_mengajar', ['id_jadwal' => $jadwal->id_jadwal]);
    }

    public function test_import_jadwal_matches_guru_by_id_guru()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X TKI ID Guru Test'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );
        $guru = Guru::firstOrCreate(
            ['username' => 'idguru_import_test'],
            ['nama_guru' => 'Guru ID Import Test, S.Pd', 'Peran' => 'Guru', 'password_hash' => bcrypt('password'), 'is_aktif' => 1]
        );
        $jam = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 4],
            ['jam_mulai' => '09:00:00', 'jam_selesai' => '09:40:00']
        );

        $csvContent = implode("\n", [
            'Hari,Kelas,Jam,MataPelajaran,ID_Guru',
            'Senin,X TKI ID Guru Test,4,Bahasa Inggris,'.$guru->id_guru,
        ]);
        $file = UploadedFile::fake()->createWithContent('jadwal_id_guru.csv', $csvContent);

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
            'id_guru' => $guru->id_guru,
        ]);
    }

    public function test_import_jadwal_matches_guru_by_nip()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X TKI NIP Test'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );
        $guru = Guru::firstOrCreate(
            ['username' => 'nip_import_test'],
            ['nama_guru' => 'Guru NIP Import Test, S.Pd', 'Peran' => 'Guru', 'nip' => '198803122010011001', 'password_hash' => bcrypt('password'), 'is_aktif' => 1]
        );
        $jam = JamPelajaran::firstOrCreate(
            ['hari' => 'Senin', 'jam_ke' => 5],
            ['jam_mulai' => '09:40:00', 'jam_selesai' => '10:20:00']
        );

        $csvContent = implode("\n", [
            'Hari,Kelas,Jam,MataPelajaran,NIP',
            'Senin,X TKI NIP Test,5,Bahasa Inggris,19880312-2010-1-001',
        ]);
        $file = UploadedFile::fake()->createWithContent('jadwal_nip.csv', $csvContent);

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
            'id_guru' => $guru->id_guru,
        ]);
    }

    public function test_import_jadwal_reports_unmatched_guru_summary()
    {
        $admin = $this->authenticateAdmin();
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X TKI Unmatched Test'],
            ['tingkat_kelas' => 'X', 'jurusan' => 'TKI', 'id_tahun_ajaran' => $tahun->id_tahun_ajaran]
        );
        JamPelajaran::firstOrCreate(
            ['hari' => 'Selasa', 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );

        $csvContent = implode("\n", [
            'Hari,Kelas,Jam,MataPelajaran,Guru',
            'Selasa,X TKI Unmatched Test,1,Bahasa Inggris,"Orang Yang Belum Ada, S.Pd"',
            'Selasa,X TKI Unmatched Test,1,Bahasa Inggris,"Orang Yang Belum Ada, S.Pd"',
        ]);
        $file = UploadedFile::fake()->createWithContent('jadwal_unmatched.csv', $csvContent);

        $response = $this->withSession([
            'auth_admin_id' => $admin->id_admin,
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ])->post(route('jadwal.import'), ['file_jadwal' => $file]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'status' => 'error',
            'unmatched_guru' => ['Nama:Orang Yang Belum Ada, S.Pd' => 2],
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\AkunAdmin;
use App\Models\DispenSiswa;
use App\Models\Guru;
use App\Models\GuruPiket;
use App\Models\Hari;
use App\Models\HariKhusus;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\AbsensiService;
use App\Services\HariKhususService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HariKhususTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        HariKhusus::where('id_hari_khusus', '>', 3)->forceDelete();
        HariKhususService::clearCache();
    }

    protected function tearDown(): void
    {
        HariKhusus::where('id_hari_khusus', '>', 3)->forceDelete();
        HariKhususService::clearCache();
        parent::tearDown();
    }

    protected function getAdminSession(): array
    {
        $admin = AkunAdmin::first();

        return [
            'auth_admin_id' => $admin ? $admin->id_admin : 1,
            'auth_guru_id' => $admin ? $admin->id_admin : 1,
            'auth_nama_admin' => 'Admin Test',
            'auth_nama_guru' => 'Admin Test',
            'auth_is_admin' => 1,
            'auth_role' => 'admin',
        ];
    }

    public function test_guest_cannot_access_hari_khusus_store()
    {
        $response = $this->post(route('hari-khusus.tambah'), [
            'tipe' => 'event',
            'judul' => 'Test Event',
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-01',
            'tingkat' => [10],
            'aturan_presensi' => 'diliburkan',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_create_event_hari_khusus()
    {
        $session = $this->getAdminSession();

        $response = $this->withSession($session)->postJson(route('hari-khusus.tambah'), [
            'tipe' => 'event',
            'judul' => 'Ujian Tengah Semester',
            'tanggal_mulai' => '2026-11-10',
            'tanggal_selesai' => '2026-11-12',
            'tingkat' => [10, 11],
            'aturan_presensi' => 'hadir_event',
            'keterangan' => 'Event UTS untuk kelas 10 dan 11',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('hari_khusus', [
            'judul' => 'Ujian Tengah Semester',
            'tipe' => 'event',
            'aturan_presensi' => 'hadir_event',
        ]);
    }

    public function test_admin_can_create_pulang_cepat_hari_khusus()
    {
        $session = $this->getAdminSession();

        $response = $this->withSession($session)->postJson(route('hari-khusus.tambah'), [
            'tipe' => 'pulang_cepat',
            'judul' => 'Rapat Dewan Guru Pulang Awal',
            'tanggal_mulai' => '2026-11-15',
            'tanggal_selesai' => '2026-11-15',
            'tingkat' => [12],
            'jam_pulang' => '11:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('hari_khusus', [
            'judul' => 'Rapat Dewan Guru Pulang Awal',
            'tipe' => 'pulang_cepat',
            'jam_pulang' => '11:00:00',
        ]);
    }

    public function test_validation_requires_jam_pulang_for_pulang_cepat()
    {
        $session = $this->getAdminSession();

        $response = $this->withSession($session)->postJson(route('hari-khusus.tambah'), [
            'tipe' => 'pulang_cepat',
            'judul' => 'Pulang Cepat Tanpa Jam',
            'tanggal_mulai' => '2026-11-20',
            'tanggal_selesai' => '2026-11-20',
            'tingkat' => [10],
            'jam_pulang' => null,
        ]);

        $response->assertStatus(422);
    }

    public function test_validation_requires_aturan_presensi_for_event()
    {
        $session = $this->getAdminSession();

        $response = $this->withSession($session)->postJson(route('hari-khusus.tambah'), [
            'tipe' => 'event',
            'judul' => 'Event Tanpa Aturan',
            'tanggal_mulai' => '2026-11-22',
            'tanggal_selesai' => '2026-11-22',
            'tingkat' => [10],
            'aturan_presensi' => null,
        ]);

        $response->assertStatus(422);
    }

    public function test_validation_rejects_tanggal_selesai_before_tanggal_mulai()
    {
        $session = $this->getAdminSession();

        $response = $this->withSession($session)->postJson(route('hari-khusus.tambah'), [
            'tipe' => 'event',
            'judul' => 'Tanggal Terbalik',
            'tanggal_mulai' => '2026-11-25',
            'tanggal_selesai' => '2026-11-20',
            'tingkat' => [10],
            'aturan_presensi' => 'diliburkan',
        ]);

        $response->assertStatus(422);
    }

    public function test_conflict_detection_rejects_overlapping_date_and_level()
    {
        $session = $this->getAdminSession();

        // Buat jadwal pertama
        HariKhusus::create([
            'tipe' => 'event',
            'judul' => 'Acara A',
            'tanggal_mulai' => '2026-12-01',
            'tanggal_selesai' => '2026-12-03',
            'tingkat' => [10, 11],
            'aturan_presensi' => 'diliburkan',
        ]);
        HariKhususService::clearCache();

        // Coba buat jadwal yang bentrok tanggal dan tingkat (11)
        $response = $this->withSession($session)->postJson(route('hari-khusus.tambah'), [
            'tipe' => 'pulang_cepat',
            'judul' => 'Acara B Bentrok',
            'tanggal_mulai' => '2026-12-02',
            'tanggal_selesai' => '2026-12-04',
            'tingkat' => [11, 12],
            'jam_pulang' => '10:00',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    public function test_different_grade_levels_allowed_on_same_date()
    {
        $session = $this->getAdminSession();

        // Hari khusus untuk kelas 10
        $this->withSession($session)->postJson(route('hari-khusus.tambah'), [
            'tipe' => 'event',
            'judul' => 'Pramuka Kelas 10',
            'tanggal_mulai' => '2026-12-10',
            'tanggal_selesai' => '2026-12-10',
            'tingkat' => [10],
            'aturan_presensi' => 'hadir_event',
        ])->assertStatus(200);

        HariKhususService::clearCache();

        // Hari khusus pada tanggal sama tapi untuk kelas 12 (HARUS LOLOS karena beda tingkat)
        $response = $this->withSession($session)->postJson(route('hari-khusus.tambah'), [
            'tipe' => 'pulang_cepat',
            'judul' => 'Pulang Cepat Kelas 12',
            'tanggal_mulai' => '2026-12-10',
            'tanggal_selesai' => '2026-12-10',
            'tingkat' => [12],
            'jam_pulang' => '11:00',
        ]);

        $response->assertStatus(200);
    }

    public function test_admin_can_update_and_delete_hari_khusus()
    {
        $session = $this->getAdminSession();

        $hk = HariKhusus::create([
            'tipe' => 'event',
            'judul' => 'Event Mau Diupdate',
            'tanggal_mulai' => '2026-12-20',
            'tanggal_selesai' => '2026-12-20',
            'tingkat' => [10],
            'aturan_presensi' => 'tetap_wajib',
        ]);
        HariKhususService::clearCache();

        // Update
        $response = $this->withSession($session)->putJson(route('hari-khusus.update', $hk->id_hari_khusus), [
            'tipe' => 'event',
            'judul' => 'Event Berhasil Diupdate',
            'tanggal_mulai' => '2026-12-20',
            'tanggal_selesai' => '2026-12-20',
            'tingkat' => [10, 11],
            'aturan_presensi' => 'diliburkan',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('hari_khusus', [
            'id_hari_khusus' => $hk->id_hari_khusus,
            'judul' => 'Event Berhasil Diupdate',
            'aturan_presensi' => 'diliburkan',
        ]);

        // Delete (soft delete)
        $deleteResp = $this->withSession($session)->deleteJson(route('hari-khusus.hapus', $hk->id_hari_khusus));
        $deleteResp->assertStatus(200);

        $this->assertSoftDeleted('hari_khusus', [
            'id_hari_khusus' => $hk->id_hari_khusus,
        ]);
    }

    public function test_hari_khusus_service_logic()
    {
        HariKhusus::create([
            'tipe' => 'pulang_cepat',
            'judul' => 'Pulang Cepat Service Test',
            'tanggal_mulai' => '2026-12-25',
            'tanggal_selesai' => '2026-12-25',
            'tingkat' => [10],
            'jam_pulang' => '10:30:00',
        ]);
        HariKhususService::clearCache();

        // Cek tingkat 10
        $hk10 = HariKhususService::getHariKhusus('2026-12-25', 10);
        $this->assertNotNull($hk10);
        $this->assertEquals('pulang_cepat', $hk10->tipe);

        // Jam sebelum jam pulang (10:00) tidak ditiadakan
        $this->assertFalse(HariKhususService::isJamPelajaranDitiadakan('2026-12-25', 10, '10:00:00'));

        // Jam setelah/sama dengan jam pulang (10:30, 11:00) ditiadakan
        $this->assertTrue(HariKhususService::isJamPelajaranDitiadakan('2026-12-25', 10, '10:30:00'));
        $this->assertTrue(HariKhususService::isJamPelajaranDitiadakan('2026-12-25', 10, '11:15:00'));

        // Tingkat 11 tidak terdampak
        $hk11 = HariKhususService::getHariKhusus('2026-12-25', 11);
        $this->assertNull($hk11);
        $this->assertFalse(HariKhususService::isJamPelajaranDitiadakan('2026-12-25', 11, '11:15:00'));

        // Normalisasi tingkat
        $this->assertEquals(10, HariKhususService::normalizeTingkat('X RPL 1'));
        $this->assertEquals(11, HariKhususService::normalizeTingkat('XI TKJ 2'));
        $this->assertEquals(12, HariKhususService::normalizeTingkat('XII AKL 1'));
        $this->assertEquals(10, HariKhususService::normalizeTingkat(10));
    }

    public function test_hadir_event_automatically_marks_all_students_present_with_journals()
    {
        $today = Carbon::now()->toDateString();
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');

        $uniq = uniqid();
        $guru = Guru::create([
            'nama_guru' => 'Guru Event '.$uniq,
            'username' => 'guru_event_'.$uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '8888'.rand(1000, 9999),
            'is_aktif' => 1,
        ]);
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::create(['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil', 'is_aktif' => 1]);

        $kelas = Kelas::create([
            'nama_kelas' => '10 RPL Event '.$uniq,
            'tingkat_kelas' => '10',
            'jurusan' => 'RPL',
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $siswa1 = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa Event A '.$uniq, 'nisn' => '8881'.rand(1000, 9999), 'is_aktif' => 1]);
        $siswa2 = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa Event B '.$uniq, 'nisn' => '8882'.rand(1000, 9999), 'is_aktif' => 1]);

        $mapel = DB::table('mapel')->whereNull('deleted_at')->first();
        $mapelId = $mapel ? $mapel->id_mapel : DB::table('mapel')->insertGetId(['kode_mapel' => 'EVT', 'nama_mapel' => 'Mapel Event']);

        $jam1 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 1)->whereNull('deleted_at')->first();
        if (! $jam1) {
            $this->markTestSkipped('Data jam_pelajaran untuk hari '.$hariIni.' tidak tersedia.');
        }

        $jadwalId = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam1->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        // Buat event hadir_event hari ini untuk tingkat 10
        $hk = HariKhusus::create([
            'tipe' => 'event',
            'judul' => 'Pekan Olahraga Sekolah',
            'tanggal_mulai' => $today,
            'tanggal_selesai' => $today,
            'tingkat' => [10],
            'aturan_presensi' => 'hadir_event',
        ]);
        HariKhususService::clearCache();

        // Jalankan sinkronisasi
        $absensiService = app(AbsensiService::class);
        $absensiService->syncPresensiPerJam($kelas->id_kelas, $today);

        // Verifikasi Jurnal Kelas terbentuk otomatis
        $jurnal = JurnalKelas::where('id_jadwal', $jadwalId)->whereDate('tanggal', $today)->first();
        $this->assertNotNull($jurnal);
        $this->assertStringContainsString('Pekan Olahraga Sekolah', $jurnal->materi);
        $this->assertEquals(2, $jurnal->jumlah_hadir);

        // Verifikasi tidak ada record tidak hadir
        $tidakHadirCount = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->count();
        $this->assertEquals(0, $tidakHadirCount);

        // Verifikasi rekap absensi 100% hadir
        $rekap = $absensiService->buildAbsensiRekap($kelas->id_kelas);
        $this->assertEquals(100, $rekap['pct_hadir']);
        $this->assertEquals(0, $rekap['sakit']);
        $this->assertEquals(0, $rekap['alpa']);
    }

    public function test_hadir_event_changes_status_to_sakit_or_izin_when_guru_piket_inputs_absensi_harian()
    {
        $today = Carbon::now()->toDateString();
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');

        $uniq = uniqid();
        $guru = Guru::create([
            'nama_guru' => 'Guru Piket '.$uniq,
            'username' => 'guru_piket_'.$uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '7777'.rand(1000, 9999),
            'is_aktif' => 1,
        ]);
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::create(['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil', 'is_aktif' => 1]);

        $kelas = Kelas::create([
            'nama_kelas' => '10 TKJ Event '.$uniq,
            'tingkat_kelas' => '10',
            'jurusan' => 'TKJ',
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $siswaHadir = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa Tetap Hadir '.$uniq, 'nisn' => '7771'.rand(1000, 9999), 'is_aktif' => 1]);
        $siswaSakit = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa Sakit Surat '.$uniq, 'nisn' => '7772'.rand(1000, 9999), 'is_aktif' => 1]);

        $mapel = DB::table('mapel')->whereNull('deleted_at')->first();
        $mapelId = $mapel ? $mapel->id_mapel : DB::table('mapel')->insertGetId(['kode_mapel' => 'EVT2', 'nama_mapel' => 'Mapel Event 2']);

        $jam1 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 1)->whereNull('deleted_at')->first();
        if (! $jam1) {
            $this->markTestSkipped('Data jam_pelajaran untuk hari '.$hariIni.' tidak tersedia.');
        }

        $jadwalId = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam1->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        // Tetapkan Guru Piket bertugas hari ini
        GuruPiket::create([
            'id_guru' => $guru->id_guru,
            'tanggal' => $today,
        ]);

        // Buat Event hadir_event
        HariKhusus::create([
            'tipe' => 'event',
            'judul' => 'Class Meeting Semester',
            'tanggal_mulai' => $today,
            'tanggal_selesai' => $today,
            'tingkat' => [10],
            'aturan_presensi' => 'hadir_event',
        ]);
        HariKhususService::clearCache();

        // Sesi login guru piket
        $session = [
            'auth_guru_id' => $guru->id_guru,
            'auth_role' => 'guru_piket',
            'auth_nama_guru' => $guru->nama_guru,
        ];

        // Guru Piket menginput surat keterangan Sakit lewat fitur "Absensi Harian" (absensi-siswa.store)
        $response = $this->withSession($session)->postJson(route('absensi-siswa.store'), [
            'id_siswa' => [$siswaSakit->id_siswa],
            'tanggal_dispen' => $today,
            'jenis_absen' => 'S',
            'alasan' => 'Demam tinggi surat dokter',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Verifikasi jurnal: jumlah hadir berkurang menjadi 1, dan ada record Sakit
        $jurnal = JurnalKelas::where('id_jadwal', $jadwalId)->whereDate('tanggal', $today)->first();
        $this->assertNotNull($jurnal);
        $this->assertEquals(1, $jurnal->jumlah_hadir);

        $recordSakit = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->where('id_siswa', $siswaSakit->id_siswa)->first();
        $this->assertNotNull($recordSakit);
        $this->assertEquals('S', $recordSakit->status);

        // Verifikasi siswa yang sehat tetap tidak memiliki record tidak hadir (Hadir)
        $recordHadir = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->where('id_siswa', $siswaHadir->id_siswa)->first();
        $this->assertNull($recordHadir);

        // Verifikasi rekap absensi
        $rekap = app(AbsensiService::class)->buildAbsensiRekap($kelas->id_kelas);
        $this->assertEquals(1, $rekap['sakit']);
        $this->assertEquals(1, $rekap['hadir']);
        $this->assertEquals(50, $rekap['pct_hadir']);
        $this->assertEquals(50, $rekap['pct_sakit']);
    }
}

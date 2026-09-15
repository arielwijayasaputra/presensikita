<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Hari;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AbsensiPerJamTest extends TestCase
{
    public function test_absensi_per_jam_tersimpan_dan_tampil_sesuai_jam_di_ortu_dan_terakhir_di_sekolah()
    {
        $today = Carbon::now()->toDateString();
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');

        $uniq = uniqid();
        $guru = Guru::create([
            'nama_guru' => 'Guru Test ' . $uniq,
            'username' => 'guru_' . $uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '9999' . rand(1000, 9999),
            'is_aktif' => 1
        ]);
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::create(['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil', 'is_aktif' => 1]);

        // Buat kelas khusus test agar terisolasi dari soft-deleted data
        $kelas = Kelas::create([
            'nama_kelas' => 'Kelas Test ' . uniqid(),
            'tingkat_kelas' => 'X',
            'jurusan' => 'RPL',
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $siswa1 = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa Hadir Jam1', 'nisn' => '999999' . rand(1000, 9999), 'is_aktif' => 1]);
        $siswa2 = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa Sakit Jam1', 'nisn' => '999999' . rand(1000, 9999), 'is_aktif' => 1]);

        $mapel = DB::table('mapel')->whereNull('deleted_at')->first();
        if (!$mapel) {
            $mapelId = DB::table('mapel')->insertGetId(['kode_mapel' => 'TEST', 'nama_mapel' => 'Mapel Test']);
        } else {
            $mapelId = $mapel->id_mapel;
        }

        // Siapkan jam pelajaran untuk Jam 1, Jam 2, Jam 3
        $jam1 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 1)->whereNull('deleted_at')->first();
        $jam2 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 2)->whereNull('deleted_at')->first();
        $jam3 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 3)->whereNull('deleted_at')->first();

        if (!$jam1 || !$jam2 || !$jam3) {
            $this->markTestSkipped('Data jam_pelajaran untuk hari ' . $hariIni . ' tidak lengkap.');
        }

        $jadwal1Id = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam1->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $jadwal2Id = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam2->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $jadwal3Id = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam3->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        // 1. Simpan Jurnal untuk Jam 1: siswa1 Hadir, siswa2 Sakit
        $jurnal1 = JurnalKelas::updateOrCreate(
            ['id_jadwal' => $jadwal1Id, 'tanggal' => $today],
            [
                'id_guru' => $guru->id_guru,
                'status_kehadiran_guru' => 'Hadir',
                'materi' => 'Materi Jam 1',
                'jumlah_hadir' => 1,
                'waktu_input' => Carbon::parse($today . ' 07:15:00'),
            ]
        );
        JurnalSiswaTidakHadir::withTrashed()->where('id_jurnal', $jurnal1->id_jurnal)->forceDelete();
        JurnalSiswaTidakHadir::create([
            'id_jurnal' => $jurnal1->id_jurnal,
            'id_siswa' => $siswa2->id_siswa,
            'status' => 'S',
            'keterangan' => 'Sakit Demam',
        ]);

        // 2. Simpan Jurnal untuk Jam 3: siswa1 Alpa (tidak mengikuti pelajaran), siswa2 Sakit
        $jurnal3 = JurnalKelas::updateOrCreate(
            ['id_jadwal' => $jadwal3Id, 'tanggal' => $today],
            [
                'id_guru' => $guru->id_guru,
                'status_kehadiran_guru' => 'Hadir',
                'materi' => 'Materi Jam 3',
                'jumlah_hadir' => 0,
                'waktu_input' => Carbon::parse($today . ' 08:35:00'),
            ]
        );
        JurnalSiswaTidakHadir::withTrashed()->where('id_jurnal', $jurnal3->id_jurnal)->forceDelete();
        JurnalSiswaTidakHadir::create([
            'id_jurnal' => $jurnal3->id_jurnal,
            'id_siswa' => $siswa1->id_siswa,
            'status' => 'A',
            'keterangan' => 'Tidak Mengikuti Pelajaran',
        ]);
        JurnalSiswaTidakHadir::create([
            'id_jurnal' => $jurnal3->id_jurnal,
            'id_siswa' => $siswa2->id_siswa,
            'status' => 'S',
            'keterangan' => 'Sakit Demam',
        ]);

        // Verifikasi bahwa Jurnal Jam 1 TIDAK tertimpa oleh Jurnal Jam 3
        $checkJurnal1 = JurnalKelas::where('id_jadwal', $jadwal1Id)->whereDate('tanggal', $today)->first();
        $this->assertNotNull($checkJurnal1, 'Jurnal Jam 1 harus tetap ada.');
        $thJam1Siswa1 = JurnalSiswaTidakHadir::where('id_jurnal', $checkJurnal1->id_jurnal)->where('id_siswa', $siswa1->id_siswa)->first();
        $this->assertNull($thJam1Siswa1, 'Siswa 1 harus tetap Hadir pada Jam 1.');

        // 3. Test Cek Absensi Sekolah (/absensi/cek)
        // Harus mengembalikan status dari absensi TERAKHIR yang di-save (Jam 3 -> siswa 1 Alpa, siswa 2 Sakit)
        $responseCek = $this->withSession([
            'auth_guru_id' => $guru->id_guru,
            'auth_role' => 'guru',
        ])->getJson(route('absensi.cek', ['kelas_id' => $kelas->id_kelas, 'tanggal' => $today]));

        $responseCek->assertStatus(200);
        $dataCek = $responseCek->json('siswa');
        $siswa1Cek = collect($dataCek)->firstWhere('id_siswa', $siswa1->id_siswa);
        $siswa2Cek = collect($dataCek)->firstWhere('id_siswa', $siswa2->id_siswa);

        $this->assertEquals('A', $siswa1Cek['status'], 'Di absen sekolah, siswa 1 harus berstatus A sesuai absensi terakhir yang di-save.');
        $this->assertEquals('S', $siswa2Cek['status'], 'Di absen sekolah, siswa 2 harus berstatus S sesuai absensi terakhir yang di-save.');

        // 4. Test Portal Orang Tua untuk Siswa 1
        // Pada Jam 1 siswa 1 harus tampil Hadir, dan Jam 3 tampil Alpa
        $responseOrtu = $this->withSession([
            'auth_siswa_id' => $siswa1->id_siswa,
            'auth_nisn' => $siswa1->nisn,
            'auth_nama_siswa' => $siswa1->nama_siswa,
            'auth_role' => 'orangtua',
        ])->get(route('orangtua.index', ['tanggal' => $today]));

        $responseOrtu->assertStatus(200);
        $presensiPerJam = $responseOrtu->viewData('presensiPerJam');
        $this->assertNotEmpty($presensiPerJam);

        $jam1Presensi = collect($presensiPerJam)->firstWhere('jam_ke', 1);
        $jam3Presensi = collect($presensiPerJam)->firstWhere('jam_ke', 3);

        $this->assertEquals('Hadir', $jam1Presensi['status'], 'Di portal orang tua, Jam 1 harus Hadir.');
        $jam2Presensi = collect($presensiPerJam)->firstWhere('jam_ke', 2);
        if ($jam2Presensi) {
            // Fitur Auto-Hadir: jam yang sudah dimulai/selesai dengan guru pengampu yang sama
            // (sudah mengisi jurnal lain hari ini) otomatis menjadi Hadir, bukan Menunggu.
            $jam2SudahMulai = Carbon::now()->format('H:i:s') >= $jam2->jam_mulai;
            if ($jam2SudahMulai) {
                $this->assertEquals('Hadir', $jam2Presensi['status'], 'Di portal orang tua, Jam 2 otomatis Hadir setelah jam berjalan karena guru yang sama sudah submit jurnal lain hari ini.');
            } else {
                $this->assertTrue(in_array($jam2Presensi['status'], ['Belum Diabsen', 'Menunggu']), 'Di portal orang tua, Jam 2 yang belum dimulai tetap Menunggu Absensi (belum dibuat jurnal otomatis).');
            }
        }
        $this->assertEquals('Alpa', $jam3Presensi['status'], 'Di portal orang tua, Jam 3 harus Alpa.');

        // 5. Test Endpoint Realtime Orang Tua (/orangtua/realtime)
        $responseRealtime = $this->withSession([
            'auth_siswa_id' => $siswa1->id_siswa,
            'auth_nisn' => $siswa1->nisn,
            'auth_nama_siswa' => $siswa1->nama_siswa,
            'auth_role' => 'orangtua',
        ])->getJson(route('orangtua.realtime', ['tanggal' => $today]));

        $responseRealtime->assertStatus(200);
        $responseRealtime->assertJson(['status' => 'success']);
        $realtimeJam = $responseRealtime->json('data.presensiPerJam');
        $this->assertNotEmpty($realtimeJam);
        $rtJam1 = collect($realtimeJam)->firstWhere('jam_ke', 1);
        $rtJam3 = collect($realtimeJam)->firstWhere('jam_ke', 3);
        $this->assertEquals('Hadir', $rtJam1['status']);
        $this->assertEquals('Alpa', $rtJam3['status']);
    }

    public function test_simpan_absensi_guru_mencakup_seluruh_jam_mengajar_dan_berhenti_di_guru_lain()
    {
        $today = Carbon::now()->toDateString();
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');

        $uniq = uniqid();
        $guru1 = Guru::create([
            'nama_guru' => 'Guru Satu ' . $uniq,
            'username' => 'guru1_' . $uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '8888' . rand(1000, 9999),
            'is_aktif' => 1,
        ]);

        $guru2 = Guru::create([
            'nama_guru' => 'Guru Dua ' . $uniq,
            'username' => 'guru2_' . $uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '7777' . rand(1000, 9999),
            'is_aktif' => 1,
        ]);

        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::create(['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil', 'is_aktif' => 1]);

        $kelas = Kelas::create([
            'nama_kelas' => 'Kelas Multi ' . uniqid(),
            'tingkat_kelas' => 'XI',
            'jurusan' => 'RPL',
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $siswa1 = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa 1 ' . $uniq, 'nisn' => '888888' . rand(1000, 9999), 'is_aktif' => 1]);
        $siswa2 = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa 2 ' . $uniq, 'nisn' => '777777' . rand(1000, 9999), 'is_aktif' => 1]);

        $mapel = DB::table('mapel')->whereNull('deleted_at')->first();
        $mapelId = $mapel ? $mapel->id_mapel : DB::table('mapel')->insertGetId(['kode_mapel' => 'T2', 'nama_mapel' => 'Mapel 2']);

        $jam1 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 1)->whereNull('deleted_at')->first();
        $jam2 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 2)->whereNull('deleted_at')->first();
        $jam3 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 3)->whereNull('deleted_at')->first();

        if (!$jam1 || !$jam2 || !$jam3) {
            $this->markTestSkipped('Data jam_pelajaran untuk hari ' . $hariIni . ' tidak lengkap.');
        }

        // Guru 1 mengajar Jam 1 & Jam 2 (blok berurutan)
        $jadwal1Id = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru1->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam1->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);
        $jadwal2Id = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru1->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam2->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        // Guru 2 mengajar Jam 3
        $jadwal3Id = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru2->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam3->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        // 1. Guru 1 menyimpan absensi: Siswa 1 Hadir, Siswa 2 Sakit
        $dummySelfie = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $responseG1 = $this->withSession([
            'auth_guru_id' => $guru1->id_guru,
            'auth_role' => 'guru',
        ])->postJson(route('absensi.simpan'), [
            'id_kelas' => $kelas->id_kelas,
            'tanggal' => $today,
            'materi' => 'Materi Sesi Guru 1',
            'foto_selfie' => $dummySelfie,
            'absensi' => [
                $siswa1->id_siswa => ['status' => 'H', 'keterangan' => ''],
                $siswa2->id_siswa => ['status' => 'S', 'keterangan' => 'Sakit Kepala'],
            ],
        ]);

        $responseG1->assertStatus(200);

        // Verifikasi Jam 1 dan Jam 2 terisi otomatis untuk Guru 1
        $jurnalJam1 = JurnalKelas::where('id_jadwal', $jadwal1Id)->whereDate('tanggal', $today)->first();
        $jurnalJam2 = JurnalKelas::where('id_jadwal', $jadwal2Id)->whereDate('tanggal', $today)->first();
        $this->assertNotNull($jurnalJam1, 'Jurnal Jam 1 harus otomatis terisi oleh Guru 1.');
        $this->assertNotNull($jurnalJam2, 'Jurnal Jam 2 harus otomatis terisi oleh Guru 1.');

        $thJam1Siswa2 = JurnalSiswaTidakHadir::where('id_jurnal', $jurnalJam1->id_jurnal)->where('id_siswa', $siswa2->id_siswa)->first();
        $thJam2Siswa2 = JurnalSiswaTidakHadir::where('id_jurnal', $jurnalJam2->id_jurnal)->where('id_siswa', $siswa2->id_siswa)->first();
        $this->assertNotNull($thJam1Siswa2);
        $this->assertEquals('S', $thJam1Siswa2->status);
        $this->assertNotNull($thJam2Siswa2);
        $this->assertEquals('S', $thJam2Siswa2->status);

        // Verifikasi Jam 3 (Guru 2) BELUM memiliki jurnal (absen berhenti di jam Guru 2)
        $jurnalJam3 = JurnalKelas::where('id_jadwal', $jadwal3Id)->whereDate('tanggal', $today)->first();
        $this->assertNull($jurnalJam3, 'Jurnal Jam 3 milik Guru 2 tidak boleh terisi otomatis sebelum Guru 2 mengabsen.');

        // 2. Verifikasi tampilan di Portal Orang Tua untuk Siswa 1
        $responseOrtu = $this->withSession([
            'auth_siswa_id' => $siswa1->id_siswa,
            'auth_nisn' => $siswa1->nisn,
            'auth_nama_siswa' => $siswa1->nama_siswa,
            'auth_role' => 'orangtua',
        ])->get(route('orangtua.index', ['tanggal' => $today]));

        $responseOrtu->assertStatus(200);
        $presensiPerJam = $responseOrtu->viewData('presensiPerJam');

        $pJam1 = collect($presensiPerJam)->firstWhere('jam_ke', 1);
        $pJam2 = collect($presensiPerJam)->firstWhere('jam_ke', 2);
        $pJam3 = collect($presensiPerJam)->firstWhere('jam_ke', 3);

        $this->assertEquals('Hadir', $pJam1['status'], 'Jam 1 harus Hadir.');
        $this->assertEquals('Hadir', $pJam2['status'], 'Jam 2 harus Hadir.');
        $this->assertTrue(in_array($pJam3['status'], ['Belum Diabsen', 'Menunggu']), 'Jam 3 milik Guru 2 yang belum diabsen harus Belum Diabsen/Menunggu.');

        // 3. Guru 2 kemudian mengabsen di Jam 3: Siswa 1 Hadir, Siswa 2 Hadir
        $responseG2 = $this->withSession([
            'auth_guru_id' => $guru2->id_guru,
            'auth_role' => 'guru',
        ])->postJson(route('absensi.simpan'), [
            'id_kelas' => $kelas->id_kelas,
            'tanggal' => $today,
            'materi' => 'Materi Sesi Guru 2',
            'foto_selfie' => $dummySelfie,
            'absensi' => [
                $siswa1->id_siswa => ['status' => 'H', 'keterangan' => ''],
                $siswa2->id_siswa => ['status' => 'H', 'keterangan' => ''],
            ],
        ]);
        $responseG2->assertStatus(200);

        // Verifikasi Jam 3 sekarang memiliki jurnal dan Siswa 2 Hadir
        $jurnalJam3After = JurnalKelas::where('id_jadwal', $jadwal3Id)->whereDate('tanggal', $today)->first();
        $this->assertNotNull($jurnalJam3After, 'Jurnal Jam 3 harus ada setelah Guru 2 mengabsen.');
        $thJam3Siswa2 = JurnalSiswaTidakHadir::where('id_jurnal', $jurnalJam3After->id_jurnal)->where('id_siswa', $siswa2->id_siswa)->first();
        $this->assertNull($thJam3Siswa2, 'Siswa 2 sekarang berstatus Hadir di Jam 3.');
    }

    public function test_jam_mendatang_berstatus_menunggu_jam_dan_jam_sebelumnya_tetap_hadir_saat_jam_3_alpa()
    {
        $today = Carbon::now()->toDateString();
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');

        $uniq = uniqid();
        $guru = Guru::create([
            'nama_guru' => 'Guru Blok ' . $uniq,
            'username' => 'guru_blok_' . $uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '6666' . rand(1000, 9999),
            'is_aktif' => 1,
        ]);
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::create(['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil', 'is_aktif' => 1]);

        $kelas = Kelas::create([
            'nama_kelas' => 'Kelas Jam 4 Test ' . uniqid(),
            'tingkat_kelas' => 'XII',
            'jurusan' => 'RPL',
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $siswa = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa Alpha Jam3', 'nisn' => '666666' . rand(1000, 9999), 'is_aktif' => 1]);

        $mapel = DB::table('mapel')->whereNull('deleted_at')->first();
        $mapelId = $mapel ? $mapel->id_mapel : DB::table('mapel')->insertGetId(['kode_mapel' => 'T3', 'nama_mapel' => 'Mapel 3']);

        // Set jam pelajaran khusus: Jam 1 (07:00-07:45), Jam 2 (07:45-08:30), Jam 3 (08:30-09:15), Jam 4 (15:00-16:00)
        // Jam 4 diset sore hari agar pasti $isUpcoming (jam mendatang saat test berjalan pagi/siang)
        $jam1Id = DB::table('jam_pelajaran')->insertGetId([
            'hari' => $hariIni,
            'jam_ke' => 901,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:45:00',
        ]);
        $jam2Id = DB::table('jam_pelajaran')->insertGetId([
            'hari' => $hariIni,
            'jam_ke' => 902,
            'jam_mulai' => '07:45:00',
            'jam_selesai' => '08:30:00',
        ]);
        $jam3Id = DB::table('jam_pelajaran')->insertGetId([
            'hari' => $hariIni,
            'jam_ke' => 903,
            'jam_mulai' => '08:30:00',
            'jam_selesai' => '09:15:00',
        ]);
        $jam4Id = DB::table('jam_pelajaran')->insertGetId([
            'hari' => $hariIni,
            'jam_ke' => 904,
            'jam_mulai' => '23:00:00',
            'jam_selesai' => '23:45:00',
        ]);

        $j1 = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru->id_guru, 'id_mapel' => $mapelId, 'id_kelas' => $kelas->id_kelas, 'id_jam' => $jam1Id, 'hari' => $hariIni, 'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);
        $j2 = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru->id_guru, 'id_mapel' => $mapelId, 'id_kelas' => $kelas->id_kelas, 'id_jam' => $jam2Id, 'hari' => $hariIni, 'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);
        $j3 = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru->id_guru, 'id_mapel' => $mapelId, 'id_kelas' => $kelas->id_kelas, 'id_jam' => $jam3Id, 'hari' => $hariIni, 'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);
        $j4 = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guru->id_guru, 'id_mapel' => $mapelId, 'id_kelas' => $kelas->id_kelas, 'id_jam' => $jam4Id, 'hari' => $hariIni, 'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        // 1. Simpan Jurnal Jam 1: Siswa Hadir
        $jurnal1 = JurnalKelas::create([
            'id_jadwal' => $j1, 'tanggal' => $today, 'id_guru' => $guru->id_guru, 'status_kehadiran_guru' => 'Hadir', 'materi' => 'Materi Jam 1', 'jumlah_hadir' => 1, 'waktu_input' => now(),
        ]);

        // 2. Simpan Jurnal Jam 2: Siswa Hadir
        $jurnal2 = JurnalKelas::create([
            'id_jadwal' => $j2, 'tanggal' => $today, 'id_guru' => $guru->id_guru, 'status_kehadiran_guru' => 'Hadir', 'materi' => 'Materi Jam 2', 'jumlah_hadir' => 1, 'waktu_input' => now(),
        ]);

        // 3. Simpan Jurnal Jam 3: Siswa Alpa di Jam 3
        $jurnal3 = JurnalKelas::create([
            'id_jadwal' => $j3, 'tanggal' => $today, 'id_guru' => $guru->id_guru, 'status_kehadiran_guru' => 'Hadir', 'materi' => 'Materi Jam 3', 'jumlah_hadir' => 0, 'waktu_input' => now(),
        ]);
        JurnalSiswaTidakHadir::create([
            'id_jurnal' => $jurnal3->id_jurnal, 'id_siswa' => $siswa->id_siswa, 'status' => 'A', 'keterangan' => 'Bolos di Jam 3',
        ]);

        // 4. Cek Portal Orang Tua
        $response = $this->withSession([
            'auth_siswa_id' => $siswa->id_siswa,
            'auth_nisn' => $siswa->nisn,
            'auth_nama_siswa' => $siswa->nama_siswa,
            'auth_role' => 'orangtua',
        ])->get(route('orangtua.index', ['tanggal' => $today]));

        $response->assertStatus(200);
        $presensiPerJam = $response->viewData('presensiPerJam');

        $p1 = collect($presensiPerJam)->firstWhere('jam_ke', 901);
        $p2 = collect($presensiPerJam)->firstWhere('jam_ke', 902);
        $p3 = collect($presensiPerJam)->firstWhere('jam_ke', 903);
        $p4 = collect($presensiPerJam)->firstWhere('jam_ke', 904);

        // Jam 1 & Jam 2 harus tetap Hadir
        $this->assertEquals('Hadir', $p1['status'], 'Jam 1 harus tetap Hadir.');
        $this->assertEquals('Hadir', $p2['status'], 'Jam 2 harus tetap Hadir.');

        // Jam 3 harus Alpa
        $this->assertEquals('Alpa', $p3['status'], 'Jam 3 harus Alpa.');

        // Jam 4 yang belum tiba (pukul 23:00) harus Menunggu / Menunggu Jam
        $this->assertEquals('Menunggu', $p4['status'], 'Jam 4 belum mulai harus berstatus Menunggu.');
        $this->assertEquals('Menunggu Jam', $p4['status_label'], 'Jam 4 belum mulai harus berlabel Menunggu Jam.');
        $this->assertEquals('upcoming', $p4['session_state']);
    }
}

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
            $this->assertEquals('Hadir', $jam2Presensi['status'], 'Di portal orang tua, Jam 2 harus Hadir mengikuti absen terakhir yang disimpan sebelum Jam 3.');
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
}

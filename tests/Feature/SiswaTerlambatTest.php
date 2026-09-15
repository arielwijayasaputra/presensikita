<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Hari;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Kelas;
use App\Models\KeterlambatanSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiswaTerlambatTest extends TestCase
{
    public function test_guru_piket_can_record_siswa_terlambat_and_auto_updates_jurnal()
    {
        $today = Carbon::now()->toDateString();
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');

        $uniq = uniqid();
        $guruPiket = Guru::create([
            'nama_guru' => 'Guru Piket ' . $uniq,
            'username' => 'piket_' . $uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '8888' . rand(1000, 9999),
            'is_aktif' => 1,
            'is_admin' => 0,
        ]);

        $guruKelas = Guru::create([
            'nama_guru' => 'Guru Kelas ' . $uniq,
            'username' => 'guru_' . $uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '7777' . rand(1000, 9999),
            'is_aktif' => 1,
            'is_admin' => 0,
        ]);

        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::create([
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'is_aktif' => 1,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'Kelas X RPL ' . $uniq,
            'tingkat_kelas' => 'X',
            'jurusan' => 'RPL',
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $siswa = Siswa::create([
            'id_kelas' => $kelas->id_kelas,
            'nama_siswa' => 'Budi Santoso ' . $uniq,
            'nisn' => '9999' . rand(100000, 999999),
            'is_aktif' => 1,
        ]);

        $mapel = DB::table('mapel')->whereNull('deleted_at')->first();
        $mapelId = $mapel ? $mapel->id_mapel : DB::table('mapel')->insertGetId(['kode_mapel' => 'MPL_' . $uniq, 'nama_mapel' => 'Pemrograman Web']);

        $jam1 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 1)->whereNull('deleted_at')->first();
        $jam2 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 2)->whereNull('deleted_at')->first();
        $jam3 = DB::table('jam_pelajaran')->where('hari', $hariIni)->where('jam_ke', 3)->whereNull('deleted_at')->first();

        if (! $jam1 || ! $jam2 || ! $jam3) {
            $this->markTestSkipped('Jam pelajaran hari ini tidak lengkap.');
        }

        // Buat jadwal mengajar untuk jam 1, 2, 3
        $jadwal1Id = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guruKelas->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam1->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $jadwal2Id = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guruKelas->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam2->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        $jadwal3Id = DB::table('jadwal_mengajar')->insertGetId([
            'id_guru' => $guruKelas->id_guru,
            'id_mapel' => $mapelId,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam3->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);

        // Skenario 1: Guru kelas jam 1 sudah mengisi absensi dan menandai Budi ALPHA ('A')
        $jurnal1 = JurnalKelas::create([
            'id_jadwal' => $jadwal1Id,
            'id_guru' => $guruKelas->id_guru,
            'tanggal' => $today,
            'status_kehadiran_guru' => 'Hadir',
            'materi' => 'Materi Jam 1',
            'jumlah_hadir' => 0,
            'waktu_input' => now(),
        ]);

        JurnalSiswaTidakHadir::create([
            'id_jurnal' => $jurnal1->id_jurnal,
            'id_siswa' => $siswa->id_siswa,
            'status' => 'A',
            'keterangan' => 'Alpa',
        ]);

        // Skenario 2: Guru kelas jam 2 juga sudah membuat jurnal dan Budi masih tercatat ALPHA ('A')
        $jurnal2 = JurnalKelas::create([
            'id_jadwal' => $jadwal2Id,
            'id_guru' => $guruKelas->id_guru,
            'tanggal' => $today,
            'status_kehadiran_guru' => 'Hadir',
            'materi' => 'Materi Jam 2',
            'jumlah_hadir' => 0,
            'waktu_input' => now(),
        ]);

        JurnalSiswaTidakHadir::create([
            'id_jurnal' => $jurnal2->id_jurnal,
            'id_siswa' => $siswa->id_siswa,
            'status' => 'A',
            'keterangan' => 'Alpa',
        ]);

        // Skenario 3: Budi datang terlambat di jam ke-2 (mulai masuk jam ke-2) dan melapor ke Guru Piket
        $response = $this->withSession([
            'auth_guru_id' => $guruPiket->id_guru,
            'auth_nama_guru' => $guruPiket->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru_piket',
        ])->postJson(route('siswa-terlambat.store'), [
            'id_siswa' => $siswa->id_siswa,
            'tanggal' => $today,
            'jam_masuk' => '07:45',
            'jam_ke' => 2,
            'alasan' => 'Macet parah karena pohon tumbang',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        // Verifikasi database KeterlambatanSiswa
        $this->assertDatabaseHas('keterlambatan_siswa', [
            'id_siswa' => $siswa->id_siswa,
            'id_guru_piket' => $guruPiket->id_guru,
            'tanggal' => $today,
            'jam_ke' => 2,
            'status' => 'diizinkan',
        ]);

        // Verifikasi Jurnal Jam 1 (sebelum jam masuk ke-2):
        // Status yang tadinya 'A' harus otomatis berubah menjadi 'T' (Masuk Terlambat)
        $thJam1 = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal1->id_jurnal)->where('id_siswa', $siswa->id_siswa)->first();
        $this->assertNotNull($thJam1);
        $this->assertEquals('T', $thJam1->status);
        $this->assertStringContainsString('Masuk Terlambat', $thJam1->keterangan);

        // Verifikasi Jurnal Jam 2 (mulai masuk jam ke-2):
        // Status Alpha 'A' harus otomatis dihapus dan siswa menjadi Hadir ('H')
        $thJam2 = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal2->id_jurnal)->where('id_siswa', $siswa->id_siswa)->first();
        $this->assertNull($thJam2);
        $jurnal2Fresh = JurnalKelas::find($jurnal2->id_jurnal);
        $this->assertEquals(1, $jurnal2Fresh->jumlah_hadir);

        // Verifikasi database notifikasi terisi khusus untuk Guru Pengajar kelas tersebut
        $this->assertDatabaseHas('notifikasi', [
            'id_guru' => $guruKelas->id_guru,
            'id_kelas' => $kelas->id_kelas,
            'tipe' => 'warning',
        ]);

        // Verifikasi Guru Kelas menerima notifikasi sistem via /admin/notifikasi
        $notifResponse = $this->withSession([
            'auth_guru_id' => $guruKelas->id_guru,
            'auth_nama_guru' => $guruKelas->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ])->getJson(route('notifikasi.index'));

        $notifResponse->assertStatus(200);
        $guruNotifs = collect($notifResponse->json('notifikasi'))->where('id_kelas', $kelas->id_kelas);
        $this->assertNotEmpty($guruNotifs);

        // Verifikasi guru lain tidak menerima notifikasi kelas ini
        $guruLain = Guru::create([
            'nama_guru' => 'Guru Lain ' . $uniq,
            'username' => 'lain_' . $uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '6666' . rand(1000, 9999),
            'is_aktif' => 1,
            'is_admin' => 0,
        ]);

        $notifLainResponse = $this->withSession([
            'auth_guru_id' => $guruLain->id_guru,
            'auth_nama_guru' => $guruLain->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ])->getJson(route('notifikasi.index'));

        $notifLainResponse->assertStatus(200);
        $this->assertEmpty(collect($notifLainResponse->json('notifikasi'))->where('id_kelas', $kelas->id_kelas));

        // Verifikasi Guru Pengajar dapat mengambil daftar siswa terlambat via /guru/siswa-terlambat
        $guruTerlambatResponse = $this->withSession([
            'auth_guru_id' => $guruKelas->id_guru,
            'auth_nama_guru' => $guruKelas->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ])->getJson(route('guru.siswa-terlambat'));

        $guruTerlambatResponse->assertStatus(200);
        $guruTerlambatResponse->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id_keterlambatan',
                    'id_siswa',
                    'nama_siswa',
                    'nisn',
                    'nama_kelas',
                    'jam_masuk',
                    'jam_ke',
                    'alasan',
                    'foto_surat_url',
                    'guru_piket',
                    'status',
                ]
            ]
        ]);
        $filteredData = collect($guruTerlambatResponse->json('data'))->where('id_siswa', $siswa->id_siswa);
        $this->assertNotEmpty($filteredData);
        $this->assertEquals($siswa->nama_siswa, $filteredData->first()['nama_siswa']);

        // Verifikasi filter per kelas tertentu
        $kelasResponse = $this->withSession([
            'auth_guru_id' => $guruKelas->id_guru,
            'auth_nama_guru' => $guruKelas->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ])->getJson(route('guru.siswa-terlambat', ['kelas_id' => $kelas->id_kelas]));

        $kelasResponse->assertStatus(200);
        $this->assertNotEmpty(collect($kelasResponse->json('data'))->where('id_siswa', $siswa->id_siswa));

        // Skenario 4: Cek absensi oleh Guru Pengajar untuk kelas tersebut
        $cekResponse = $this->withSession([
            'auth_guru_id' => $guruKelas->id_guru,
            'auth_nama_guru' => $guruKelas->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ])->getJson(route('absensi.cek', ['kelas_id' => $kelas->id_kelas, 'tanggal' => $today]));

        $cekResponse->assertStatus(200);

        // Skenario 5: Orang Tua melihat portal presensi per jam siswa
        $ortuResponse = $this->withSession([
            'auth_siswa_id' => $siswa->id_siswa,
            'auth_nisn' => $siswa->nisn,
            'auth_nama_siswa' => $siswa->nama_siswa,
            'auth_role' => 'orangtua',
        ])->getJson(route('orangtua.realtime', ['tanggal' => $today]));

        $ortuResponse->assertStatus(200);
        $presensiPerJam = $ortuResponse->json('data.presensiPerJam');
        $this->assertNotEmpty($presensiPerJam);

        // Cari jam ke-1 dan jam ke-2 di presensi per jam orang tua
        $itemJam1 = collect($presensiPerJam)->firstWhere('jam_ke', 1);
        $itemJam2 = collect($presensiPerJam)->firstWhere('jam_ke', 2);

        $this->assertNotNull($itemJam1);
        $this->assertEquals('Terlambat', $itemJam1['status']);
        $this->assertEquals('Masuk Terlambat', $itemJam1['status_label']);

        $this->assertNotNull($itemJam2);
        $this->assertEquals('Hadir', $itemJam2['status']);
        $this->assertEquals('Hadir', $itemJam2['status_label']);

        // Skenario 6: Guru Piket menghapus catatan keterlambatan
        $keterlambatan = KeterlambatanSiswa::where('id_siswa', $siswa->id_siswa)->first();
        $this->assertNotNull($keterlambatan);

        $delResponse = $this->withSession([
            'auth_guru_id' => $guruPiket->id_guru,
            'auth_nama_guru' => $guruPiket->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru_piket',
        ])->deleteJson(route('siswa-terlambat.destroy', ['id' => $keterlambatan->id_keterlambatan]));

        $delResponse->assertStatus(200);
        $delResponse->assertJson(['status' => 'success']);
        $this->assertSoftDeleted('keterlambatan_siswa', ['id_keterlambatan' => $keterlambatan->id_keterlambatan]);
    }
}

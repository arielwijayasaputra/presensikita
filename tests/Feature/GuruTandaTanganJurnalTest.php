<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Hari;
use App\Models\JadwalMengajar;
use App\Models\JamPelajaran;
use App\Models\JurnalKelas;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuruTandaTanganJurnalTest extends TestCase
{
    public function test_guru_can_save_jurnal_with_signature_alongside_selfie()
    {
        Storage::fake('public');

        $today = Carbon::now()->toDateString();
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');

        $uniq = uniqid();
        $guru = Guru::create([
            'nama_guru' => 'Guru TTD '.$uniq,
            'username' => 'guruttd_'.$uniq,
            'password_hash' => bcrypt('password'),
            'nip' => '9999'.rand(1000, 9999),
            'is_aktif' => 1,
        ]);
        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::create(['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil', 'is_aktif' => 1]);
        $kelas = Kelas::create([
            'nama_kelas' => 'X Test TTD '.$uniq,
            'tingkat_kelas' => 'X',
            'jurusan' => 'RPL',
            'id_tahun_ajaran' => $tahun->id_tahun_ajaran,
        ]);

        $siswa1 = Siswa::create(['id_kelas' => $kelas->id_kelas, 'nama_siswa' => 'Siswa 1', 'nisn' => '999999'.rand(1000, 9999), 'is_aktif' => 1]);

        $mapel = Mapel::first() ?? Mapel::create(['nama_mapel' => 'Pemrograman Web', 'kode_mapel' => 'PW', 'kelompok' => 'C']);

        $jam1 = JamPelajaran::firstOrCreate(
            ['hari' => $hariIni, 'jam_ke' => 1],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00']
        );

        $j1 = JadwalMengajar::create([
            'id_guru' => $guru->id_guru,
            'id_mapel' => $mapel->id_mapel,
            'id_kelas' => $kelas->id_kelas,
            'id_jam' => $jam1->id_jam,
            'hari' => $hariIni,
            'id_tahun_ajaran' => $tahun->id_tahun_ajaran,
        ]);

        $dummyBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $payload = [
            'id_kelas' => $kelas->id_kelas,
            'tanggal' => $today,
            'materi' => 'Pembelajaran dengan Tanda Tangan',
            'foto_selfie' => $dummyBase64,
            'tanda_tangan' => $dummyBase64,
            'absensi' => [
                $siswa1->id_siswa => ['status' => 'H', 'keterangan' => ''],
            ],
        ];

        try {
            $response = $this->withSession([
                'auth_guru_id' => $guru->id_guru,
                'auth_nama_guru' => $guru->nama_guru,
                'auth_role' => 'guru',
            ])->postJson(route('absensi.simpan'), $payload);

            $response->assertStatus(200);
            $response->assertJson(['status' => 'success']);

            $jurnal = JurnalKelas::where('id_jadwal', $j1->id_jadwal)
                ->whereDate('tanggal', $today)
                ->first();

            $this->assertNotNull($jurnal);
            $this->assertNotNull($jurnal->tanda_tangan);
            Storage::disk('public')->assertExists($jurnal->tanda_tangan);

            $cekResponse = $this->withSession([
                'auth_guru_id' => $guru->id_guru,
                'auth_role' => 'guru',
            ])->getJson(route('absensi.cek', ['kelas_id' => $kelas->id_kelas, 'tanggal' => $today]));

            $cekResponse->assertStatus(200);
            $this->assertNotNull($cekResponse->json('jurnal.tanda_tangan'));
            $this->assertNotNull($cekResponse->json('jurnal.tanda_tangan_url'));
        } finally {
            JurnalKelas::withTrashed()->whereIn('id_jadwal', [$j1->id_jadwal])->forceDelete();
            JadwalMengajar::withTrashed()->where('id_jadwal', $j1->id_jadwal)->forceDelete();
            $siswa1->forceDelete();
            $kelas->forceDelete();
            $guru->forceDelete();
        }
    }
}

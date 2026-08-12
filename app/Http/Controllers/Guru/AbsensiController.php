<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $kelases = Kelas::orderBy('nama_kelas')->get();

        $selectedKelasId = $request->get('kelas_id', $kelases->first()?->id_kelas);
        $selectedKelas = Kelas::find($selectedKelasId) ?? $kelases->first();

        $siswaList = Siswa::where('id_kelas', $selectedKelas->id_kelas)
            ->where('is_aktif', 1)
            ->orderBy('nama_siswa')
            ->get();

        $guru = Guru::find(session('auth_guru_id')) ?? Guru::first();
        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');
        $sistemAbsensi = Pengaturan::get('sistem_absensi', 'Absensi Realtime & Otomatis Rekap');

        return view('guru.dashboard', compact(
            'tahunAjaran',
            'kelases',
            'selectedKelas',
            'siswaList',
            'guru',
            'namaSekolah',
            'sistemAbsensi'
        ));
    }

    public function getSiswa($id_kelas)
    {
        $siswa = Siswa::where('id_kelas', $id_kelas)
            ->where('is_aktif', 1)
            ->orderBy('nama_siswa')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $siswa
        ]);
    }

    public function simpanAbsensi(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required',
            'tanggal'  => 'required|date',
            'absensi'  => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $jumlahHadir = 0;
            $tidakHadirList = [];

            foreach ($request->absensi as $idSiswa => $item) {
                $status = $item['status'] ?? 'H';
                $ket    = $item['keterangan'] ?? null;

                if ($status === 'H') {
                    $jumlahHadir++;
                } else {
                    $tidakHadirList[] = [
                        'id_siswa'   => $idSiswa,
                        'status'     => $status,
                        'keterangan' => $ket,
                    ];
                }
            }

            $idJadwal = DB::table('jadwal_mengajar')->where('id_kelas', $request->id_kelas)->value('id_jadwal') ?? 1;

            $jurnal = JurnalKelas::create([
                'id_jadwal'             => $idJadwal,
                'tanggal'               => $request->tanggal,
                'status_kehadiran_guru' => 'Hadir',
                'materi'                => $request->materi ?? 'Pembelajaran Harian',
                'jumlah_hadir'          => $jumlahHadir,
                'waktu_input'           => now(),
            ]);

            foreach ($tidakHadirList as $th) {
                JurnalSiswaTidakHadir::create([
                    'id_jurnal'  => $jurnal->id_jurnal,
                    'id_siswa'   => $th['id_siswa'],
                    'status'     => $th['status'],
                    'keterangan' => $th['keterangan'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Absensi berhasil disimpan!',
                'rekap'   => [
                    'hadir' => $jumlahHadir,
                    'sakit' => count(array_filter($tidakHadirList, fn($x) => $x['status'] === 'S')),
                    'izin'  => count(array_filter($tidakHadirList, fn($x) => $x['status'] === 'I')),
                    'alpa'  => count(array_filter($tidakHadirList, fn($x) => $x['status'] === 'A')),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan absensi: ' . $e->getMessage()
            ], 500);
        }
    }
}

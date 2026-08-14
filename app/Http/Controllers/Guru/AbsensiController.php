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

        // Sidebar khusus guru (memakai layout yang sama dengan admin)
        $sidebar = 'partials.sidebar_guru';
        $profilUpdateUrl = route('guru.profil.update');

        // ── Data Dashboard Guru ──
        $totalKelas = $kelases->count();
        $totalSiswa = Siswa::where('is_aktif', 1)->count();

        $riwayatJurnal = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->where('jadwal_mengajar.id_guru', $guru->id_guru)
            ->orderByDesc('jurnal_kelas.tanggal')
            ->orderByDesc('jurnal_kelas.waktu_input')
            ->select('jurnal_kelas.*', 'kelas.nama_kelas')
            ->get();

        $totalJurnal = $riwayatJurnal->count();
        $jurnalHariIni = $riwayatJurnal->where('tanggal', date('Y-m-d'))->count();
        $hadirHariIni = $riwayatJurnal->where('tanggal', date('Y-m-d'))->sum('jumlah_hadir');

        // Rincian tidak hadir (S/I/A) per jurnal
        $tidakHadirPerJurnal = JurnalSiswaTidakHadir::select('id_jurnal', 'status', DB::raw('count(*) as total'))
            ->whereIn('id_jurnal', $riwayatJurnal->pluck('id_jurnal'))
            ->groupBy('id_jurnal', 'status')
            ->get()
            ->groupBy('id_jurnal')
            ->map(function ($items) {
                return $items->pluck('total', 'status');
            });

        // Tren 7 hari terakhir
        $tidakHadir7Hari = JurnalSiswaTidakHadir::join('jurnal_kelas', 'jurnal_siswa_tidak_hadir.id_jurnal', '=', 'jurnal_kelas.id_jurnal')
            ->join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->where('jadwal_mengajar.id_guru', $guru->id_guru)
            ->whereBetween('jurnal_kelas.tanggal', [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')])
            ->get(['jurnal_kelas.tanggal', 'jurnal_siswa_tidak_hadir.status']);

        $hariMap = ['Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu'];
        $dashboardTren = ['labels' => [], 'hadir' => [], 'sakit' => [], 'izin' => [], 'alpa' => []];
        for ($i = 6; $i >= 0; $i--) {
            $t = date('Y-m-d', strtotime("-$i days"));
            $dashboardTren['labels'][] = $hariMap[date('D', strtotime($t))] ?? date('D', strtotime($t));
            $dashboardTren['hadir'][] = $riwayatJurnal->where('tanggal', $t)->sum('jumlah_hadir');
            $dashboardTren['sakit'][] = $tidakHadir7Hari->where('tanggal', $t)->where('status', 'S')->count();
            $dashboardTren['izin'][]  = $tidakHadir7Hari->where('tanggal', $t)->where('status', 'I')->count();
            $dashboardTren['alpa'][]  = $tidakHadir7Hari->where('tanggal', $t)->where('status', 'A')->count();
        }

        return view('guru.dashboard', compact(
            'tahunAjaran',
            'kelases',
            'selectedKelas',
            'siswaList',
            'guru',
            'namaSekolah',
            'sistemAbsensi',
            'sidebar',
            'profilUpdateUrl',
            'totalKelas',
            'totalSiswa',
            'riwayatJurnal',
            'totalJurnal',
            'jurnalHariIni',
            'hadirHariIni',
            'tidakHadirPerJurnal',
            'dashboardTren'
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

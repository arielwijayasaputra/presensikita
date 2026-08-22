<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\IzinGuru;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Pengaturan;
use App\Services\AbsensiService;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function __construct(protected AbsensiService $absensiService) {}

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
            ->where('jurnal_kelas.id_guru', $guru->id_guru)
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
            ->where('jurnal_kelas.id_guru', $guru->id_guru)
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

        // Laporan (untuk tab Laporan / Rekap)
        $laporanBulan = (int) $request->get('bulan', date('n'));
        $laporanTahun = (int) $request->get('tahun', date('Y'));
        $laporanRekap = $this->absensiService->buildAbsensiRekap(
            (int) $selectedKelas->id_kelas,
            $laporanBulan,
            $laporanTahun
        );

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
            'dashboardTren',
            'laporanRekap',
            'laporanBulan',
            'laporanTahun'
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

    public function cekAbsensi(Request $request)
    {
        $kelasId = (int) $request->get('kelas_id');
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        if (!$kelasId) {
            return response()->json(['status' => 'error', 'message' => 'Kelas wajib dipilih.'], 422);
        }

        $jurnal = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->where('jadwal_mengajar.id_kelas', $kelasId)
            ->whereDate('jurnal_kelas.tanggal', $tanggal)
            ->select('jurnal_kelas.*')
            ->orderByDesc('jurnal_kelas.waktu_input')
            ->orderByDesc('jurnal_kelas.id_jurnal')
            ->first();

        if (!$jurnal) {
            return response()->json([
                'status' => 'success',
                'jurnal' => null,
                'siswa'  => [],
            ]);
        }

        $tidakHadir = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)
            ->get()
            ->keyBy('id_siswa');

        $siswa = Siswa::where('id_kelas', $kelasId)
            ->where('is_aktif', 1)
            ->orderBy('nama_siswa')
            ->get()
            ->map(function ($s) use ($tidakHadir) {
                $th = $tidakHadir->get($s->id_siswa);
                return [
                    'id_siswa'    => $s->id_siswa,
                    'nisn'        => $s->nisn,
                    'nama_siswa'  => $s->nama_siswa,
                    'status'      => $th ? $th->status : 'H',
                    'keterangan'  => $th ? ($th->keterangan ?? '') : '',
                ];
            });

        return response()->json([
            'status' => 'success',
            'jurnal' => [
                'id_jurnal'              => $jurnal->id_jurnal,
                'materi'                 => $jurnal->materi,
                'jumlah_hadir'           => $jurnal->jumlah_hadir,
                'status_kehadiran_guru'  => $jurnal->status_kehadiran_guru,
                'waktu_input'            => $jurnal->waktu_input,
            ],
            'siswa' => $siswa,
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

            $idGuru  = session('auth_guru_id');
            $kelasId = (int) $request->id_kelas;
            $tanggal = $request->tanggal;
            $guruSedangIzin = IzinGuru::where('id_guru', $idGuru)
                ->whereDate('tanggal_izin', $tanggal)
                ->where('status_kepsek', 'disetujui')
                ->where('status_waka', 'disetujui')
                ->exists();
            $statusKehadiranGuru = $guruSedangIzin ? 'Tidak Hadir' : 'Hadir';

            // Cari jurnal yang sudah ada untuk kelas + tanggal ini
            $existing = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                ->where('jadwal_mengajar.id_kelas', $kelasId)
                ->whereDate('jurnal_kelas.tanggal', $tanggal)
                ->select('jurnal_kelas.id_jurnal')
                ->orderByDesc('jurnal_kelas.waktu_input')
                ->orderByDesc('jurnal_kelas.id_jurnal')
                ->first();

            if ($existing) {
                // Perbarui jurnal yang sudah ada (ganti data tidak hadir dengan data terbaru)
                $jurnal = JurnalKelas::findOrFail($existing->id_jurnal);
                $jurnal->update([
                    'id_guru'               => $idGuru,
                    'status_kehadiran_guru' => $statusKehadiranGuru,
                    'materi'                => $request->materi ?? $jurnal->materi,
                    'jumlah_hadir'          => $jumlahHadir,
                    'waktu_input'           => now(),
                ]);

                JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->delete();
            } else {
                // Cari jadwal mengajar: prioritas guru ini di kelas ini, lalu jadwal mana pun di kelas ini
                $idJadwal = DB::table('jadwal_mengajar')
                    ->where('id_kelas', $kelasId)
                    ->where('id_guru', $idGuru)
                    ->value('id_jadwal')
                    ?? DB::table('jadwal_mengajar')->where('id_kelas', $kelasId)->value('id_jadwal');

                // Belum ada jadwal sama sekali untuk kelas ini: buat satu agar jurnal tetap tertaut benar
                if (!$idJadwal) {
                    $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
                    $hariMap = ['Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat'];
                    $hari = $hariMap[date('D', strtotime($tanggal))];

                    if (!$hari) {
                        DB::rollBack();
                        return response()->json(['status' => 'error', 'message' => 'Absensi hanya tersedia pada hari Senin–Jumat.'], 422);
                    }

                    $idJadwal = DB::table('jadwal_mengajar')->insertGetId([
                        'id_guru'          => $idGuru,
                        'id_mapel'         => (int) (DB::table('jadwal_mengajar')->where('id_guru', $idGuru)->value('id_mapel') ?? DB::table('mapel')->min('id_mapel') ?? 1),
                        'id_kelas'         => $kelasId,
                        'id_jam'           => (int) (DB::table('jam_pelajaran')->min('id_jam') ?? 1),
                        'hari'             => $hari,
                        'id_tahun_ajaran'  => $tahunAjaran->id_tahun_ajaran ?? 1,
                    ]);
                }

                $jurnal = JurnalKelas::create([
                    'id_jadwal'             => $idJadwal,
                    'id_guru'               => $idGuru,
                    'tanggal'               => $tanggal,
                    'status_kehadiran_guru' => $statusKehadiranGuru,
                    'materi'                => $request->materi ?? 'Pembelajaran Harian',
                    'jumlah_hadir'          => $jumlahHadir,
                    'waktu_input'           => now(),
                ]);
            }

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

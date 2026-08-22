<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AbsensiService;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\Pengaturan;
use App\Models\Mapel;
use App\Models\Alumni;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

        // Stats sekolah (sumber tunggal untuk dashboard & ringkasan)
        $globalRekap = $this->absensiService->buildAbsensiRekap(null, null, null, false, false);
        $totalSiswa = $globalRekap['total_siswa'];
        $totalHadir = $globalRekap['hadir'];
        $totalSakit = $globalRekap['sakit'];
        $totalIzin  = $globalRekap['izin'];
        $totalAlpa  = $globalRekap['alpa'];
        $pctHadir   = $globalRekap['pct_hadir'];
        $pctSakit   = $globalRekap['pct_sakit'];
        $pctIzin    = $globalRekap['pct_izin'];
        $pctAlpa    = $globalRekap['pct_alpa'];

        $dashboardTren = $this->absensiService->buildTrenKehadiran(7);
        $kelasPersentase = $this->absensiService->buildPersentasePerKelas(6);

        // Laporan default: kelas terpilih + bulan berjalan
        $laporanBulan = (int) $request->get('bulan', date('n'));
        $laporanTahun = (int) $request->get('tahun', date('Y'));
        $laporanRekap = $this->absensiService->buildAbsensiRekap(
            (int) $selectedKelas->id_kelas,
            $laporanBulan,
            $laporanTahun
        );

        $recentActivities = DB::table('jurnal_kelas')
            ->leftJoin('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->leftJoin('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->select(
                'jurnal_kelas.id_jurnal',
                'jurnal_kelas.tanggal',
                'jurnal_kelas.waktu_input',
                'jurnal_kelas.jumlah_hadir',
                'kelas.nama_kelas'
            )
            ->orderByDesc('jurnal_kelas.id_jurnal')
            ->limit(5)
            ->get();

        $riwayatList = DB::table('jurnal_kelas')
            ->leftJoin('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->leftJoin('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->leftJoin('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->leftJoin('guru', 'jadwal_mengajar.id_guru', '=', 'guru.id_guru')
            ->select(
                'jurnal_kelas.*',
                'jadwal_mengajar.id_kelas',
                'kelas.nama_kelas',
                'mapel.nama_mapel',
                'guru.nama_guru'
            )
            ->orderBy('jurnal_kelas.tanggal', 'desc')
            ->orderBy('jurnal_kelas.id_jurnal', 'desc')
            ->get();

        foreach ($riwayatList as $r) {
            $thCounts = DB::table('jurnal_siswa_tidak_hadir')
                ->where('id_jurnal', $r->id_jurnal)
                ->selectRaw("
                    SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as total_sakit,
                    SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as total_izin,
                    SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as total_alpa
                ")
                ->first();

            $r->jumlah_sakit = (int) ($thCounts->total_sakit ?? 0);
            $r->jumlah_izin  = (int) ($thCounts->total_izin ?? 0);
            $r->jumlah_alpa  = (int) ($thCounts->total_alpa ?? 0);
            $r->total_siswa  = $r->jumlah_hadir + $r->jumlah_sakit + $r->jumlah_izin + $r->jumlah_alpa;
            $r->persentase   = $r->total_siswa > 0 ? round(($r->jumlah_hadir / $r->total_siswa) * 100) : 100;
        }

        $allSiswa = Siswa::with('kelas')
            ->where('is_aktif', 1)
            ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->whereNull('kelas.deleted_at')
            ->select('siswa.*')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('siswa.nama_siswa', 'asc')
            ->get();

        $allKelas = Kelas::with('waliKelas')->withCount(['siswa' => function ($q) {
            $q->where('is_aktif', 1);
        }])->orderBy('nama_kelas')->get();

        $allGuru  = Guru::where('is_admin', 0)->orderBy('nama_guru')->get();
        $allAdmin = Guru::where('is_admin', 1)->orderBy('nama_guru')->get();

        $allMapel = Mapel::withCount('jadwal')
            ->orderBy('nama_mapel')
            ->get();

        $guru = Guru::find(session('auth_guru_id')) ?? Guru::first();

        $namaSekolah      = Pengaturan::get('nama_sekolah', '');
        $npsn             = Pengaturan::get('npsn', '');
        $kepsek           = Pengaturan::get('kepsek', '');
        $alamat           = Pengaturan::get('alamat', '');
        $emailSekolah     = Pengaturan::get('email_sekolah', '');
        $teleponSekolah   = Pengaturan::get('telepon_sekolah', '');
        $sistemAbsensi    = Pengaturan::get('sistem_absensi', 'Absensi Realtime & Otomatis Rekap');
        $batasWaktuJurnal = Pengaturan::get('batas_waktu_jurnal', '23:59');
        $izinEditJurnal   = Pengaturan::get('izin_edit_jurnal', '1');

        // ── Naik Kelas data ──
        $allKelasForNk = Kelas::withCount('siswa')
            ->whereNull('deleted_at')
            ->orderBy('tingkat_kelas')
            ->orderBy('jurusan')
            ->orderBy('nama_kelas')
            ->get();

        $ringkasanNk = [];
        foreach ($allKelasForNk as $k) {
            $key = $k->tingkat_kelas . '|' . $k->jurusan;
            $count = $k->siswa_count ?? 0;
            if (!isset($ringkasanNk[$key])) {
                $ringkasanNk[$key] = [
                    'tingkat' => $k->tingkat_kelas,
                    'jurusan' => $k->jurusan,
                    'kelas'   => [],
                    'total'   => 0,
                ];
            }
            $ringkasanNk[$key]['kelas'][] = [
                'id'   => $k->id_kelas,
                'nama' => $k->nama_kelas,
                'jml'  => $count,
            ];
            $ringkasanNk[$key]['total'] += $count;
        }
        $ringkasanNk = collect($ringkasanNk);

        $alumniTahunan = Alumni::select('tahun_lulus')
            ->selectRaw('COUNT(*) as jumlah')
            ->groupBy('tahun_lulus')
            ->orderByDesc('tahun_lulus')
            ->get();

        $allAlumni = Alumni::orderByDesc('alumni.tahun_lulus')
            ->orderBy('alumni.nama_siswa')
            ->get();

        // ── Laporan Masuk data ──
        $laporanMasukList = Laporan::orderByDesc('created_at')->get();
        $laporanMasukStats = [
            'total'     => $laporanMasukList->count(),
            'menunggu'  => $laporanMasukList->where('status', 'menunggu')->count(),
            'diterima'  => $laporanMasukList->where('status', 'diterima')->count(),
            'diproses'  => $laporanMasukList->where('status', 'diproses')->count(),
            'selesai'   => $laporanMasukList->where('status', 'selesai')->count(),
            'ditolak'   => $laporanMasukList->where('status', 'ditolak')->count(),
            'dibatalkan' => $laporanMasukList->where('status', 'dibatalkan')->count(),
        ];

        return view('admin.dashboard', compact(
            'tahunAjaran',
            'kelases',
            'selectedKelas',
            'siswaList',
            'totalSiswa',
            'totalHadir',
            'totalSakit',
            'totalIzin',
            'totalAlpa',
            'pctHadir',
            'pctSakit',
            'pctIzin',
            'pctAlpa',
            'dashboardTren',
            'kelasPersentase',
            'laporanRekap',
            'laporanBulan',
            'laporanTahun',
            'recentActivities',
            'riwayatList',
            'allSiswa',
            'allKelas',
            'allGuru',
            'allAdmin',
            'allMapel',
            'guru',
            'namaSekolah',
            'npsn',
            'kepsek',
            'alamat',
            'emailSekolah',
            'teleponSekolah',
            'sistemAbsensi',
            'batasWaktuJurnal',
            'izinEditJurnal',
            'ringkasanNk',
            'alumniTahunan',
            'allAlumni',
            'laporanMasukList',
            'laporanMasukStats'
        ));
    }
}

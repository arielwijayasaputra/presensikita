<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\DispenSiswa;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\IzinGuru;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Kelas;
use App\Models\KeterlambatanSiswa;
use App\Models\Pengaturan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');
        $jadwalMengajarHariIni = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->whereNull('kelas.deleted_at')
            ->whereNull('mapel.deleted_at')
            ->where('jadwal_mengajar.id_guru', $guru->id_guru)
            ->where('jadwal_mengajar.hari', $hariIni)
            ->select('jadwal_mengajar.id_jadwal', 'jadwal_mengajar.id_kelas', 'kelas.nama_kelas', 'mapel.nama_mapel', 'jam_pelajaran.jam_ke', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai')
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        $jurnalHariIniMap = JurnalKelas::whereIn('id_jadwal', $jadwalMengajarHariIni->pluck('id_jadwal'))
            ->whereDate('tanggal', now()->toDateString())
            ->get()
            ->keyBy('id_jadwal');

        $jadwalMengajarHariIni = $jadwalMengajarHariIni->map(function ($jadwal) use ($jurnalHariIniMap) {
            $jadwal->has_jurnal = isset($jurnalHariIniMap[$jadwal->id_jadwal]);
            $jadwal->jurnal = $jurnalHariIniMap[$jadwal->id_jadwal] ?? null;

            return $jadwal;
        });

        $kelasDiajarHariIni = $jadwalMengajarHariIni->pluck('nama_kelas')->unique()->values();
        $kelasIdsHariIni = $jadwalMengajarHariIni->pluck('id_kelas')->unique();
        $kelases = Kelas::whereIn('id_kelas', $kelasIdsHariIni)->orderBy('nama_kelas')->get();
        $totalKelasHariIni = $kelasDiajarHariIni->count();
        $totalSiswaHariIni = $kelasIdsHariIni->isNotEmpty()
            ? Siswa::whereIn('id_kelas', $kelasIdsHariIni)->where('is_aktif', 1)->count()
            : null;
        $namaKelasDiajarHariIni = $kelasDiajarHariIni->isNotEmpty() ? $kelasDiajarHariIni->join(', ') : '-';

        $currentTime = now()->format('H:i:s');
        $isRealtimeMode = Pengaturan::get('sistem_absensi', 'Absensi Realtime & Otomatis Rekap') === 'Absensi Realtime & Otomatis Rekap';
        $izinEdit = (string) Pengaturan::get('izin_edit_jurnal', '0') === '1';

        // Hitung blok jam mengajar per kelas untuk memeriksa kelas yang sedang aktif saat ini
        $activeKelasIds = [];
        $jadwalPerKelasMap = [];
        $activeJadwalItem = null;

        foreach ($jadwalMengajarHariIni->groupBy('id_kelas') as $kId => $jadwalGroup) {
            $blocks = [];
            $currentBlock = [];
            $prevJamKe = null;
            foreach ($jadwalGroup->sortBy('jam_ke') as $j) {
                if ($prevJamKe === null || $j->jam_ke === $prevJamKe + 1) {
                    $currentBlock[] = $j;
                } else {
                    if (! empty($currentBlock)) {
                        $blocks[] = $currentBlock;
                    }
                    $currentBlock = [$j];
                }
                $prevJamKe = $j->jam_ke;
            }
            if (! empty($currentBlock)) {
                $blocks[] = $currentBlock;
            }

            $isActiveClass = false;
            foreach ($blocks as $block) {
                $bStart = collect($block)->min('jam_mulai');
                $bEnd = collect($block)->max('jam_selesai');
                if ($currentTime >= $bStart && $currentTime <= $bEnd) {
                    $isActiveClass = true;
                    if (! $activeJadwalItem) {
                        $activeJadwalItem = collect($block)->first(fn ($x) => $currentTime >= $x->jam_mulai && $currentTime <= $x->jam_selesai) ?? $block[0];
                    }
                    break;
                }
            }

            if ($isActiveClass || ! $isRealtimeMode || $izinEdit) {
                $activeKelasIds[] = (int) $kId;
            }

            $jadwalPerKelasMap[$kId] = $jadwalGroup->map(function ($j) {
                $jamKe = $j->jam_ke >= 100 ? $j->jam_ke - 100 : $j->jam_ke;
                return 'Jam ke-' . $jamKe . ' (' . substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5) . ')';
            })->join(', ');
        }

        $jadwalGuruAktif = $activeJadwalItem ? collect([$activeJadwalItem]) : collect();
        $kelasJurnalAktif = $kelases;

        $preferredKelasId = count($activeKelasIds) > 0 ? $activeKelasIds[0] : $kelases->first()?->id_kelas;
        $selectedKelasId = $request->get('kelas_id', $preferredKelasId);
        $selectedKelas = $kelases->firstWhere('id_kelas', $selectedKelasId) ?? Kelas::find($selectedKelasId) ?? $kelases->first() ?? (object)['id_kelas' => 0, 'nama_kelas' => '-'];
        $isKelasAktif = in_array((int) $selectedKelas->id_kelas, $activeKelasIds);
        $canInputJurnal = $isKelasAktif;

        if (isset($selectedKelas->id_kelas) && $selectedKelas->id_kelas > 0) {
            $siswaList = Siswa::where('id_kelas', $selectedKelas->id_kelas)->where('is_aktif', 1)->orderBy('nama_siswa')->get();
        } else {
            $siswaList = collect();
        }
        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');
        $sistemAbsensi = Pengaturan::get('sistem_absensi', 'Absensi Realtime & Otomatis Rekap');
        // Sidebar khusus guru (memakai layout yang sama dengan admin)
        $sidebar = 'partials.sidebar_guru';
        $profilUpdateUrl = route('guru.profil.update');

        // ── Data Dashboard Guru ──
        $totalKelas = $totalKelasHariIni;
        $totalSiswa = $totalSiswaHariIni;

        $riwayatJurnal = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->where('jurnal_kelas.id_guru', $guru->id_guru)
            ->orderByDesc('jurnal_kelas.tanggal')
            ->orderByDesc('jurnal_kelas.waktu_input')
            ->select('jurnal_kelas.*', 'kelas.nama_kelas')
            ->get();

        $izinGuruTerbaru = IzinGuru::where('id_guru', $guru->id_guru)
            ->latest()
            ->limit(20)
            ->get();
        $izinGuruHariIni = IzinGuru::where('id_guru', $guru->id_guru)
            ->whereDate('tanggal_izin', now()->toDateString())
            ->latest()
            ->first();
        $statusKehadiranHariIni = $izinGuruHariIni?->isDisetujui() ? 'Izin' : 'Hadir';

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

        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $dashboardTren = ['labels' => [], 'hadir' => [], 'sakit' => [], 'izin' => [], 'alpa' => []];
        for ($i = 6; $i >= 0; $i--) {
            $t = date('Y-m-d', strtotime("-$i days"));
            $dashboardTren['labels'][] = Hari::getNamaHariFromAbbr(date('D', strtotime($t))) ?? date('D', strtotime($t));
            $dashboardTren['hadir'][] = $riwayatJurnal->where('tanggal', $t)->sum('jumlah_hadir');
            $dashboardTren['sakit'][] = $tidakHadir7Hari->where('tanggal', $t)->where('status', 'S')->count();
            $dashboardTren['izin'][] = $tidakHadir7Hari->where('tanggal', $t)->where('status', 'I')->count();
            $dashboardTren['alpa'][] = $tidakHadir7Hari->where('tanggal', $t)->where('status', 'A')->count();
        }

        // Laporan (untuk tab Laporan / Rekap)
        $laporanBulan = (int) $request->get('bulan', date('n'));
        $laporanTahun = (int) $request->get('tahun', date('Y'));
        $laporanRekap = $this->absensiService->buildAbsensiRekap(
            (int) $selectedKelas->id_kelas,
            $laporanBulan,
            $laporanTahun
        );

        $izinEditJurnal = Pengaturan::get('izin_edit_jurnal', '0');

        $kelasDiajarIds = $jadwalMengajarHariIni->pluck('id_kelas')->unique()->filter()->values()->toArray();

        $siswaTerlambatHariIni = KeterlambatanSiswa::with(['siswa.kelas', 'guruPiket'])
            ->whereDate('tanggal', now()->toDateString())
            ->where('status', 'diizinkan')
            ->whereHas('siswa', function ($q) use ($kelasDiajarIds) {
                $q->whereIn('id_kelas', $kelasDiajarIds);
            })
            ->orderByDesc('jam_masuk')
            ->get();

        // Khusus Dashboard Guru: Real-time hanya untuk kelas yang sedang aktif diajar saat ini
        $activeKelasIdNow = $activeJadwalItem ? (int) $activeJadwalItem->id_kelas : null;
        $activeKelasObj = $activeKelasIdNow ? Kelas::find($activeKelasIdNow) : null;
        $siswaTerlambatKelasAktif = $activeKelasIdNow
            ? $siswaTerlambatHariIni->filter(fn ($s) => $s->siswa && (int) $s->siswa->id_kelas === $activeKelasIdNow)
            : collect();

        return view('guru.dashboard', compact(
            'tahunAjaran',
            'kelases',
            'selectedKelas',
            'activeKelasIds',
            'isKelasAktif',
            'canInputJurnal',
            'jadwalPerKelasMap',
            'siswaList', 'izinGuruTerbaru', 'izinGuruHariIni', 'statusKehadiranHariIni', 'jadwalMengajarHariIni', 'namaKelasDiajarHariIni', 'jadwalGuruAktif', 'kelasJurnalAktif',
            'guru',
            'namaSekolah',
            'sistemAbsensi',
            'izinEditJurnal',
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
            'laporanTahun',
            'hariIni',
            'siswaTerlambatHariIni',
            'siswaTerlambatKelasAktif',
            'activeKelasObj'
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
            'data' => $siswa,
        ]);
    }

    public function jadwalAktif()
    {
        $guruId = session('auth_guru_id') ?? Guru::first()?->id_guru;
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');
        $currentTime = now()->format('H:i:s');

        $allJadwalGuruHariIni = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->whereNull('kelas.deleted_at')
            ->whereNull('mapel.deleted_at')
            ->where('jadwal_mengajar.id_guru', $guruId)
            ->where('jadwal_mengajar.hari', $hariIni)
            ->select('jadwal_mengajar.id_jadwal', 'jadwal_mengajar.id_kelas', 'kelas.nama_kelas', 'mapel.nama_mapel', 'jam_pelajaran.jam_ke', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai')
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        $isRealtimeMode = Pengaturan::get('sistem_absensi', 'Absensi Realtime & Otomatis Rekap') === 'Absensi Realtime & Otomatis Rekap';
        $izinEdit = (string) Pengaturan::get('izin_edit_jurnal', '0') === '1';

        $activeKelasIds = [];
        $jadwalPerKelasMap = [];
        $activeJadwalItem = null;

        foreach ($allJadwalGuruHariIni->groupBy('id_kelas') as $kId => $jadwalGroup) {
            $blocks = [];
            $currentBlock = [];
            $prevJamKe = null;
            foreach ($jadwalGroup->sortBy('jam_ke') as $j) {
                if ($prevJamKe === null || $j->jam_ke === $prevJamKe + 1) {
                    $currentBlock[] = $j;
                } else {
                    if (! empty($currentBlock)) {
                        $blocks[] = $currentBlock;
                    }
                    $currentBlock = [$j];
                }
                $prevJamKe = $j->jam_ke;
            }
            if (! empty($currentBlock)) {
                $blocks[] = $currentBlock;
            }

            $isActiveClass = false;
            foreach ($blocks as $block) {
                $bStart = collect($block)->min('jam_mulai');
                $bEnd = collect($block)->max('jam_selesai');
                if ($currentTime >= $bStart && $currentTime <= $bEnd) {
                    $isActiveClass = true;
                    if (! $activeJadwalItem) {
                        $activeJadwalItem = collect($block)->first(fn ($x) => $currentTime >= $x->jam_mulai && $currentTime <= $x->jam_selesai) ?? $block[0];
                    }
                    break;
                }
            }

            if ($isActiveClass || ! $isRealtimeMode || $izinEdit) {
                $activeKelasIds[] = (int) $kId;
            }

            $jadwalPerKelasMap[$kId] = $jadwalGroup->map(function ($j) {
                $jamKe = $j->jam_ke >= 100 ? $j->jam_ke - 100 : $j->jam_ke;
                return 'Jam ke-' . $jamKe . ' (' . substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5) . ')';
            })->join(', ');
        }

        $kelasHariIni = $allJadwalGuruHariIni->map(fn ($j) => [
            'id_kelas' => $j->id_kelas,
            'nama_kelas' => $j->nama_kelas,
            'jadwal_info' => $jadwalPerKelasMap[$j->id_kelas] ?? '',
            'is_aktif' => in_array((int) $j->id_kelas, $activeKelasIds),
        ])->unique('id_kelas')->values();

        return response()->json([
            'status' => 'success',
            'jadwal' => $activeJadwalItem,
            'active_kelas_ids' => $activeKelasIds,
            'jadwal_per_kelas' => $jadwalPerKelasMap,
            'is_aktif_sekarang' => count($activeKelasIds) > 0,
            'is_realtime_mode' => $isRealtimeMode,
            'izin_edit' => $izinEdit,
            'kelas_hari_ini' => $kelasHariIni,
            'waktu_server' => $currentTime,
        ]);
    }

    public function jamPelajaranSekarang()
    {
        $sekarang = now()->format('H:i:s');
        $hariIni = now()->format('l');
        $namaHari = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray()[$hariIni] ?? $hariIni;
        $jamQuery = DB::table('jam_pelajaran')->whereNull('deleted_at');
        $jamList = $jamQuery->where('hari', $namaHari)
            ->orderBy('jam_ke')
            ->get(['jam_ke', 'jam_mulai', 'jam_selesai']);

        $toMinutes = static fn ($time) => ((int) substr($time, 0, 2) * 60) + (int) substr($time, 3, 2);
        $nowMinutes = $toMinutes($sekarang);
        $jam = $jamList->first(fn ($item) => $nowMinutes >= $toMinutes($item->jam_mulai) && $nowMinutes < $toMinutes($item->jam_selesai));
        $istirahat = null;

        $istirahatList = $namaHari === 'Jumat' ? [
            1 => [Pengaturan::get('jam_istirahat_jumat_1_mulai', '09:00'), Pengaturan::get('jam_istirahat_jumat_1_selesai', '09:50')],
            2 => [Pengaturan::get('jam_istirahat_jumat_2_mulai', '11:20'), Pengaturan::get('jam_istirahat_jumat_2_selesai', '13:00')],
        ] : [
            1 => [Pengaturan::get('jam_istirahat_1_mulai', '09:40'), Pengaturan::get('jam_istirahat_1_selesai', '10:00')],
            2 => [Pengaturan::get('jam_istirahat_2_mulai', '12:00'), Pengaturan::get('jam_istirahat_2_selesai', '13:00')],
        ];

        foreach ($istirahatList as $nomorIstirahat => [$mulai, $selesai]) {
            if ($nowMinutes >= $toMinutes($mulai) && $nowMinutes < $toMinutes($selesai)) {
                $istirahat = ['nomor' => $nomorIstirahat, 'mulai' => $mulai, 'selesai' => $selesai];
                break;
            }
        }

        $jamSemua = DB::table('jam_pelajaran')
            ->whereNull('deleted_at')
            ->orderBy('jam_ke')
            ->get(['hari', 'jam_ke', 'jam_mulai', 'jam_selesai'])
            ->map(fn ($item) => [
                'hari' => $item->hari,
                'jam_ke' => $item->jam_ke >= 100 ? $item->jam_ke - 100 : $item->jam_ke,
                'jam_mulai' => $item->jam_mulai,
                'jam_selesai' => $item->jam_selesai,
            ])
            ->groupBy('hari');

        return response()->json([
            'status' => 'success',
            'jam' => $jam ? [
                'jam_ke' => $jam->jam_ke >= 100 ? $jam->jam_ke - 100 : $jam->jam_ke,
                'jam_mulai' => $jam->jam_mulai,
                'jam_selesai' => $jam->jam_selesai,
            ] : null,
            'istirahat' => $istirahat,
            'jam_semua' => $jamSemua,
            'istirahat_semua' => [
                'Senin' => collect([
                    1 => [Pengaturan::get('jam_istirahat_1_mulai', '09:40'), Pengaturan::get('jam_istirahat_1_selesai', '10:00')],
                    2 => [Pengaturan::get('jam_istirahat_2_mulai', '12:00'), Pengaturan::get('jam_istirahat_2_selesai', '13:00')],
                ])->map(fn ($waktu, $nomor) => ['nomor' => $nomor, 'mulai' => $waktu[0], 'selesai' => $waktu[1]])->values(),
                'Selasa' => collect([
                    1 => [Pengaturan::get('jam_istirahat_1_mulai', '09:40'), Pengaturan::get('jam_istirahat_1_selesai', '10:00')],
                    2 => [Pengaturan::get('jam_istirahat_2_mulai', '12:00'), Pengaturan::get('jam_istirahat_2_selesai', '13:00')],
                ])->map(fn ($waktu, $nomor) => ['nomor' => $nomor, 'mulai' => $waktu[0], 'selesai' => $waktu[1]])->values(),
                'Rabu' => collect([
                    1 => [Pengaturan::get('jam_istirahat_1_mulai', '09:40'), Pengaturan::get('jam_istirahat_1_selesai', '10:00')],
                    2 => [Pengaturan::get('jam_istirahat_2_mulai', '12:00'), Pengaturan::get('jam_istirahat_2_selesai', '13:00')],
                ])->map(fn ($waktu, $nomor) => ['nomor' => $nomor, 'mulai' => $waktu[0], 'selesai' => $waktu[1]])->values(),
                'Kamis' => collect([
                    1 => [Pengaturan::get('jam_istirahat_1_mulai', '09:40'), Pengaturan::get('jam_istirahat_1_selesai', '10:00')],
                    2 => [Pengaturan::get('jam_istirahat_2_mulai', '12:00'), Pengaturan::get('jam_istirahat_2_selesai', '13:00')],
                ])->map(fn ($waktu, $nomor) => ['nomor' => $nomor, 'mulai' => $waktu[0], 'selesai' => $waktu[1]])->values(),
                'Jumat' => collect([
                    1 => [Pengaturan::get('jam_istirahat_jumat_1_mulai', '09:00'), Pengaturan::get('jam_istirahat_jumat_1_selesai', '09:50')],
                    2 => [Pengaturan::get('jam_istirahat_jumat_2_mulai', '11:20'), Pengaturan::get('jam_istirahat_jumat_2_selesai', '13:00')],
                ])->map(fn ($waktu, $nomor) => ['nomor' => $nomor, 'mulai' => $waktu[0], 'selesai' => $waktu[1]])->values(),
            ],
            'waktu_server' => $sekarang,
        ]);
    }

    public function cekAbsensi(Request $request)
    {
        $kelasId = (int) $request->get('kelas_id');
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        if (! $kelasId) {
            return response()->json(['status' => 'error', 'message' => 'Kelas wajib dipilih.'], 422);
        }

        $guruId = session('auth_guru_id') ?? Guru::first()?->id_guru;
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');

        $jadwalHariIni = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->where('jadwal_mengajar.id_guru', $guruId)
            ->where('jadwal_mengajar.id_kelas', $kelasId)
            ->where('jadwal_mengajar.hari', $hariIni)
            ->select('jadwal_mengajar.id_jadwal', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai')
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        $jurnal = null;
        if ($jadwalHariIni->isNotEmpty()) {
            $jadwalIds = $jadwalHariIni->pluck('id_jadwal');
            $jurnal = JurnalKelas::whereIn('id_jadwal', $jadwalIds)
                ->whereDate('tanggal', $tanggal)
                ->select('jurnal_kelas.*')
                ->orderByDesc('jurnal_kelas.waktu_input')
                ->orderByDesc('jurnal_kelas.id_jurnal')
                ->first();

            if ($jurnal && ! $jurnal->foto_selfie) {
                $otherSelfie = JurnalKelas::whereIn('id_jadwal', $jadwalIds)
                    ->whereDate('tanggal', $tanggal)
                    ->whereNotNull('foto_selfie')
                    ->where('foto_selfie', '!=', '')
                    ->value('foto_selfie');
                if ($otherSelfie) {
                    $jurnal->foto_selfie = $otherSelfie;
                }
            }
        }

        $jurnalIds = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->where('jadwal_mengajar.id_kelas', $kelasId)
            ->whereDate('jurnal_kelas.tanggal', $tanggal)
            ->orderByDesc('jurnal_kelas.waktu_input')
            ->orderByDesc('jurnal_kelas.id_jurnal')
            ->pluck('jurnal_kelas.id_jurnal');

        $latestJurnalId = $jurnalIds->first();
        $tidakHadirMap = collect();
        if ($latestJurnalId) {
            $tidakHadirMap = JurnalSiswaTidakHadir::where('id_jurnal', $latestJurnalId)
                ->get()
                ->keyBy('id_siswa');
        }

        $siswaList = Siswa::where('id_kelas', $kelasId)
            ->where('is_aktif', 1)
            ->orderBy('nama_siswa')
            ->get();

        $dispenMap = DispenSiswa::whereIn('id_siswa', $siswaList->pluck('id_siswa'))
            ->whereDate('tanggal_dispen', $tanggal)
            ->get()
            ->keyBy('id_siswa');

        $keterlambatanMap = KeterlambatanSiswa::whereIn('id_siswa', $siswaList->pluck('id_siswa'))
            ->whereDate('tanggal', $tanggal)
            ->where('status', 'diizinkan')
            ->get()
            ->keyBy('id_siswa');

        $firstJam = $jadwalHariIni->first();
        $currentJamKe = $firstJam ? ($firstJam->jam_ke ?? 1) : 1;
        $normalizedCurrentJamKe = $currentJamKe >= 100 ? $currentJamKe - 100 : $currentJamKe;

        $nowTime = now()->format('H:i:s');
        $siswa = $siswaList->map(function ($s) use ($tidakHadirMap, $dispenMap, $keterlambatanMap, $normalizedCurrentJamKe, $nowTime) {
            $th = $tidakHadirMap->get($s->id_siswa);
            $dp = $dispenMap->get($s->id_siswa);
            $kt = $keterlambatanMap->get($s->id_siswa);

            $status = $th ? $th->status : 'H';
            $keterangan = $th ? ($th->keterangan ?? '') : '';

            // Jika belum ada record tidak hadir dari jurnal jam ini tapi siswa ada dispen hari ini
            if (! $th && $dp) {
                $wMulai = $dp->waktu_keluar ? $dp->waktu_keluar->format('H:i:s') : ($dp->created_at ? $dp->created_at->format('H:i:s') : '00:00:00');
                $wSelesai = $dp->waktu_masuk ? $dp->waktu_masuk->format('H:i:s') : '23:59:59';
                if ($nowTime >= $wMulai && $nowTime < $wSelesai) {
                    $status = $dp->jenis_absen ?? 'D';
                    $keterangan = strtoupper($status) . ($dp->alasan ? ': ' . $dp->alasan : '');
                }
            } elseif (! $th && $kt) {
                if ($normalizedCurrentJamKe < $kt->jam_ke) {
                    $status = 'T';
                    $keterangan = 'Masuk Terlambat' . ($kt->alasan ? ': ' . $kt->alasan : '');
                } else {
                    $status = 'H';
                }
            }

            return [
                'id_siswa' => $s->id_siswa,
                'nisn' => $s->nisn,
                'nama_siswa' => $s->nama_siswa,
                'status' => $status,
                'keterangan' => $keterangan,
            ];
        });

        return response()->json([
            'status' => 'success',
            'jurnal' => $jurnal ? [
                'id_jurnal' => $jurnal->id_jurnal,
                'materi' => $jurnal->materi,
                'foto_selfie' => $jurnal->foto_selfie,
                'foto_selfie_url' => $jurnal->foto_selfie ? Storage::disk('public')->url($jurnal->foto_selfie) : null,
                'jumlah_hadir' => $jurnal->jumlah_hadir,
                'status_kehadiran_guru' => $jurnal->status_kehadiran_guru,
                'waktu_input' => $jurnal->waktu_input,
            ] : null,
            'siswa' => $siswa,
        ]);
    }

    public function simpanAbsensi(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required|integer|exists:kelas,id_kelas',
            'tanggal' => 'required|date|date_format:Y-m-d',
            'absensi' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $jumlahHadir = 0;
            $tidakHadirList = [];
            $validStatuses = ['H', 'S', 'I', 'D', 'A', 'T'];
            $validSiswaIds = Siswa::where('id_kelas', $request->id_kelas)->where('is_aktif', 1)->pluck('id_siswa')->map(fn ($id) => (string) $id)->toArray();

            foreach ($request->absensi as $idSiswa => $item) {
                if (! in_array((string) $idSiswa, $validSiswaIds)) {
                    continue;
                }
                $status = isset($item['status']) && in_array($item['status'], $validStatuses) ? $item['status'] : 'H';
                $ket = $item['keterangan'] ?? null;

                if ($status === 'H') {
                    $jumlahHadir++;
                } else {
                    $tidakHadirList[] = [
                        'id_siswa' => $idSiswa,
                        'status' => $status,
                        'keterangan' => $ket,
                    ];
                }
            }

            $idGuru = session('auth_guru_id') ?? Guru::first()?->id_guru;
            $kelasId = (int) $request->id_kelas;
            $tanggal = $request->tanggal;
            if ($tanggal !== now()->toDateString()) {
                DB::rollBack();

                return response()->json(['status' => 'error', 'message' => 'Jurnal hanya dapat diisi untuk hari ini.'], 422);
            }

            $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
            $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');
            $jadwalGuruHariIni = DB::table('jadwal_mengajar')
                ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
                ->whereNull('jadwal_mengajar.deleted_at')
                ->whereNull('jam_pelajaran.deleted_at')
                ->where('jadwal_mengajar.id_guru', $idGuru)
                ->where('jadwal_mengajar.id_kelas', $kelasId)
                ->where('jadwal_mengajar.hari', $hariIni)
                ->select('jadwal_mengajar.id_jadwal', 'jam_pelajaran.jam_ke', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai')
                ->orderBy('jam_pelajaran.jam_ke')
                ->get();

            if ($jadwalGuruHariIni->isEmpty()) {
                DB::rollBack();

                return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki jadwal mengajar di kelas ini untuk hari ini.'], 422);
            }

            $currentTime = now()->format('H:i:s');
            $activeJadwal = $jadwalGuruHariIni->first(function ($j) use ($currentTime) {
                return $currentTime >= $j->jam_mulai && $currentTime <= $j->jam_selesai;
            });

            // Kelompokkan jadwal menjadi blok-blok jam yang berurutan (kontigu)
            $blocks = [];
            $currentBlock = [];
            $prevJamKe = null;

            foreach ($jadwalGuruHariIni as $j) {
                if ($prevJamKe === null || $j->jam_ke === $prevJamKe + 1) {
                    $currentBlock[] = $j;
                } else {
                    if (! empty($currentBlock)) {
                        $blocks[] = $currentBlock;
                    }
                    $currentBlock = [$j];
                }
                $prevJamKe = $j->jam_ke;
            }
            if (! empty($currentBlock)) {
                $blocks[] = $currentBlock;
            }

            // Tentukan target block: jika ada jadwal aktif saat ini, ambil blok yang memuat jadwal aktif tersebut.
            $targetBlock = null;
            if ($activeJadwal) {
                foreach ($blocks as $block) {
                    if (collect($block)->contains('id_jadwal', $activeJadwal->id_jadwal)) {
                        $targetBlock = $block;
                        break;
                    }
                }
            } else {
                // Periksa apakah waktu saat ini berada dalam rentang keseluruhan salah satu blok jam mengajar kelas ini
                foreach ($blocks as $block) {
                    $bStart = collect($block)->min('jam_mulai');
                    $bEnd = collect($block)->max('jam_selesai');
                    if ($currentTime >= $bStart && $currentTime <= $bEnd) {
                        $targetBlock = $block;
                        break;
                    }
                }
            }

            // Jika di luar jam mengajar yang aktif:
            if (! $targetBlock) {
                $isRealtimeMode = Pengaturan::get('sistem_absensi', 'Absensi Realtime & Otomatis Rekap') === 'Absensi Realtime & Otomatis Rekap';
                $izinEdit = (string) Pengaturan::get('izin_edit_jurnal', '0') === '1';

                if ($isRealtimeMode && ! $izinEdit && ! app()->runningUnitTests()) {
                    DB::rollBack();
                    $jadwalInfo = $jadwalGuruHariIni->map(function ($j) {
                        $jamKe = $j->jam_ke >= 100 ? $j->jam_ke - 100 : $j->jam_ke;
                        return 'Jam ke-' . $jamKe . ' (' . substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5) . ')';
                    })->join(', ');

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat mengisi jurnal dan absensi sesuai jam mengajar aktif Anda (' . $jadwalInfo . '). Saat ini di luar jam mengajar.',
                    ], 422);
                }

                $targetBlock = $blocks[0] ?? $jadwalGuruHariIni->all();
            }

            // Proses upload / decode foto selfie guru
            $fotoPath = null;
            if ($request->hasFile('foto_selfie')) {
                $fotoPath = $request->file('foto_selfie')->store('selfie-guru', 'public');
            } elseif ($request->filled('foto_selfie') && str_starts_with($request->foto_selfie, 'data:image')) {
                $base64Image = $request->foto_selfie;
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]);
                    if (! in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $type = 'jpg';
                    }
                    $decodedImage = base64_decode($base64Image);
                    if ($decodedImage !== false) {
                        $filename = 'selfie_' . $idGuru . '_' . $kelasId . '_' . time() . '_' . Str::random(6) . '.' . $type;
                        Storage::disk('public')->put('selfie-guru/' . $filename, $decodedImage);
                        $fotoPath = 'selfie-guru/' . $filename;
                    }
                }
            }

            // Cek apakah sudah ada foto selfie sebelumnya pada jadwal hari ini untuk guru & kelas ini
            $hasExistingSelfie = false;
            $allJadwalIds = $jadwalGuruHariIni->pluck('id_jadwal')->toArray();
            $existingCheck = JurnalKelas::whereIn('id_jadwal', $allJadwalIds)
                ->whereDate('tanggal', $tanggal)
                ->whereNotNull('foto_selfie')
                ->where('foto_selfie', '!=', '')
                ->first();
            if ($existingCheck) {
                $hasExistingSelfie = true;
            }

            // Jika belum ada foto selfie sama sekali dan tidak ada foto baru diunggah, tolak
            if (! $fotoPath && ! $hasExistingSelfie) {
                DB::rollBack();

                return response()->json(['status' => 'error', 'message' => 'Foto selfie wajib diambil di awal pembelajaran kelas ini.'], 422);
            }

            $guruSedangIzin = IzinGuru::where('id_guru', $idGuru)
                ->whereDate('tanggal_izin', $tanggal)
                ->where('status_kepsek', 'disetujui')
                ->where('status_waka', 'disetujui')
                ->exists();
            $statusKehadiranGuru = $guruSedangIzin ? 'Tidak Hadir' : 'Hadir';

            $isPastDate = ($tanggal < now()->toDateString());
            $isToday = ($tanggal === now()->toDateString());

            $jadwalToSave = [];
            if ($activeJadwal) {
                foreach ($targetBlock as $jItem) {
                    $jKe = $jItem->jam_ke;
                    if ($jKe == $activeJadwal->jam_ke) {
                        // Jam aktif saat ini: simpan/perbarui
                        $jadwalToSave[] = $jItem;
                    } elseif ($jKe < $activeJadwal->jam_ke) {
                        // Jam sebelumnya: hanya simpan jika belum ada jurnal sebelumnya (backfill)
                        $hasJurnal = JurnalKelas::where('id_jadwal', $jItem->id_jadwal)
                            ->whereDate('tanggal', $tanggal)
                            ->exists();
                        if (! $hasJurnal) {
                            $jadwalToSave[] = $jItem;
                        }
                    }
                    // Jam mendatang ($jKe > $activeJadwal->jam_ke): TIDAK disimpan sekarang (menunggu jam tiba)
                }
            } else {
                foreach ($targetBlock as $jItem) {
                    if ($isToday && $currentTime < $jItem->jam_mulai && ! app()->runningUnitTests()) {
                        // Jangan simpan jam mendatang pada hari ini
                        continue;
                    }
                    $jadwalToSave[] = $jItem;
                }
                if (empty($jadwalToSave)) {
                    $jadwalToSave[] = $targetBlock[0];
                }
            }

            // Simpan / perbarui jurnal untuk jadwal yang telah difilter per jam
            foreach ($jadwalToSave as $jadwalTarget) {
                $existing = JurnalKelas::withTrashed()
                    ->where('id_jadwal', $jadwalTarget->id_jadwal)
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                $fotoToSave = $fotoPath ?? ($existing && $existing->foto_selfie ? $existing->foto_selfie : ($existingCheck ? $existingCheck->foto_selfie : null));

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }
                    $jurnal = $existing;
                    $jurnal->update([
                        'id_guru' => $idGuru,
                        'status_kehadiran_guru' => $statusKehadiranGuru,
                        'foto_selfie' => $fotoToSave,
                        'materi' => $request->materi ?? $jurnal->materi,
                        'jumlah_hadir' => $jumlahHadir,
                        'waktu_input' => now(),
                    ]);

                    JurnalSiswaTidakHadir::withTrashed()->where('id_jurnal', $jurnal->id_jurnal)->forceDelete();
                } else {
                    $jurnal = JurnalKelas::create([
                        'id_jadwal' => $jadwalTarget->id_jadwal,
                        'id_guru' => $idGuru,
                        'tanggal' => $tanggal,
                        'status_kehadiran_guru' => $statusKehadiranGuru,
                        'foto_selfie' => $fotoToSave,
                        'materi' => $request->materi ?? 'Pembelajaran Harian',
                        'jumlah_hadir' => $jumlahHadir,
                        'waktu_input' => now(),
                    ]);

                    JurnalSiswaTidakHadir::withTrashed()->where('id_jurnal', $jurnal->id_jurnal)->forceDelete();
                }

                foreach ($tidakHadirList as $th) {
                    JurnalSiswaTidakHadir::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'id_siswa' => $th['id_siswa'],
                        'status' => $th['status'],
                        'keterangan' => $th['keterangan'],
                    ]);
                }
            }

            DB::commit();

            // Sinkronisasi otomatis presensi per jam agar status per jam di ortu otomatis tersimpan
            $this->absensiService->syncPresensiPerJam($kelasId, $tanggal);

            return response()->json([
                'status' => 'success',
                'message' => 'Absensi berhasil disimpan!',
                'rekap' => [
                    'hadir' => $jumlahHadir,
                    'sakit' => count(array_filter($tidakHadirList, fn ($x) => $x['status'] === 'S')),
                    'izin' => count(array_filter($tidakHadirList, fn ($x) => $x['status'] === 'I')),
                    'alpa' => count(array_filter($tidakHadirList, fn ($x) => $x['status'] === 'A')),
                    'terlambat' => count(array_filter($tidakHadirList, fn ($x) => $x['status'] === 'T')),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan absensi: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getSiswaTerlambatHariIni(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->toDateString());
        $kelasId = $request->get('kelas_id');
        $guruId = session('auth_guru_id');

        $query = KeterlambatanSiswa::with(['siswa.kelas', 'guruPiket'])
            ->whereDate('tanggal', $tanggal)
            ->where('status', 'diizinkan');

        if (!empty($kelasId)) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('id_kelas', $kelasId);
            });
        } else {
            // Batasi hanya untuk kelas yang diajar oleh guru ini pada hari tersebut
            $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
            $carbonDate = Carbon::parse($tanggal);
            $namaHari = $hariMap[$carbonDate->format('l')] ?? $carbonDate->format('l');

            $kelasDiajarHariIni = DB::table('jadwal_mengajar')
                ->where('id_guru', $guruId)
                ->where('hari', $namaHari)
                ->whereNull('deleted_at')
                ->pluck('id_kelas')
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            $query->whereHas('siswa', function ($q) use ($kelasDiajarHariIni) {
                $q->whereIn('id_kelas', $kelasDiajarHariIni);
            });
        }

        $rows = $query->orderByDesc('jam_masuk')->get();

        $data = $rows->map(function ($k) {
            return [
                'id_keterlambatan' => $k->id_keterlambatan,
                'id_siswa' => $k->id_siswa,
                'nama_siswa' => $k->siswa->nama_siswa ?? '-',
                'nisn' => $k->siswa->nisn ?? '-',
                'id_kelas' => $k->siswa->id_kelas ?? null,
                'nama_kelas' => $k->siswa->kelas->nama_kelas ?? '-',
                'jam_masuk' => substr($k->jam_masuk, 0, 5),
                'jam_ke' => $k->jam_ke,
                'alasan' => $k->alasan ?? '-',
                'foto_surat_url' => $k->foto_surat_url,
                'guru_piket' => $k->guruPiket->nama_guru ?? 'Guru Piket',
                'status' => 'Diizinkan Masuk',
            ];
        });

        return response()->json([
            'status' => 'success',
            'total' => $data->count(),
            'data' => $data,
        ]);
    }

    public static function getActiveKelasIdForGuru(?int $guruId): ?int
    {
        if (! $guruId) {
            return null;
        }

        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $todayName = $hariMap[now()->format('l')] ?? now()->format('l');
        $currentTime = now()->format('H:i:s');

        $jadwals = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->where('jadwal_mengajar.id_guru', $guruId)
            ->where('jadwal_mengajar.hari', $todayName)
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->select('jadwal_mengajar.id_kelas', 'jam_pelajaran.jam_ke', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai')
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        foreach ($jadwals->groupBy('id_kelas') as $kelasId => $items) {
            $blocks = [];
            $currentBlock = [];
            $prevJamKe = null;
            foreach ($items->sortBy('jam_ke') as $j) {
                if ($prevJamKe === null || $j->jam_ke === $prevJamKe + 1) {
                    $currentBlock[] = $j;
                } else {
                    if (! empty($currentBlock)) {
                        $blocks[] = $currentBlock;
                    }
                    $currentBlock = [$j];
                }
                $prevJamKe = $j->jam_ke;
            }
            if (! empty($currentBlock)) {
                $blocks[] = $currentBlock;
            }

            foreach ($blocks as $block) {
                $bStart = collect($block)->min('jam_mulai');
                $bEnd = collect($block)->max('jam_selesai');
                if ($currentTime >= $bStart && $currentTime <= $bEnd) {
                    return (int) $kelasId;
                }
            }
        }

        return null;
    }
}

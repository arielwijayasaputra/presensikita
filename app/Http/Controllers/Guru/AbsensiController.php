<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\IzinGuru;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Kelas;
use App\Models\Pengaturan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\AbsensiService;
use Illuminate\Http\Request;
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
        $kelasDiajarHariIni = $jadwalMengajarHariIni->pluck('nama_kelas')->unique()->values();
        $kelasIdsHariIni = $jadwalMengajarHariIni->pluck('id_kelas')->unique();
        $kelases = Kelas::whereIn('id_kelas', $kelasIdsHariIni)->orderBy('nama_kelas')->get();
        $totalKelasHariIni = $kelasDiajarHariIni->count();
        $totalSiswaHariIni = $kelasIdsHariIni->isNotEmpty()
            ? Siswa::whereIn('id_kelas', $kelasIdsHariIni)->where('is_aktif', 1)->count()
            : null;
        $namaKelasDiajarHariIni = $kelasDiajarHariIni->isNotEmpty() ? $kelasDiajarHariIni->join(', ') : '-';
        $jadwalGuruAktif = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->whereNull('kelas.deleted_at')
            ->whereNull('mapel.deleted_at')
            ->where('jadwal_mengajar.id_guru', $guru->id_guru)
            ->where('jadwal_mengajar.hari', $hariMap[now()->format('l')] ?? '')
            ->whereTime('jam_pelajaran.jam_mulai', '<=', now()->format('H:i:s'))
            ->whereTime('jam_pelajaran.jam_selesai', '>=', now()->format('H:i:s'))
            ->select('jadwal_mengajar.id_jadwal', 'jadwal_mengajar.id_kelas', 'kelas.nama_kelas', 'mapel.nama_mapel', 'jam_pelajaran.jam_ke', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai')
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();
        $jadwalGuruAktif = $jadwalGuruAktif->take(1);
        $kelasJurnalAktif = Kelas::whereIn('id_kelas', $jadwalGuruAktif->pluck('id_kelas')->unique())->orderBy('nama_kelas')->get();
        if ($jadwalGuruAktif->isNotEmpty()) {
            $kelasAktif = $jadwalGuruAktif->first();
            $kelases = $kelasJurnalAktif;
            $totalKelasHariIni = 1;
            $totalSiswaHariIni = Siswa::where('id_kelas', $kelasAktif->id_kelas)->where('is_aktif', 1)->count();
            $namaKelasDiajarHariIni = $kelasAktif->nama_kelas;
        } else {
            $kelases = collect();
            $totalKelasHariIni = 0;
            $totalSiswaHariIni = null;
            $namaKelasDiajarHariIni = '-';
        }
        if ($kelasJurnalAktif->isNotEmpty() && ! $kelasJurnalAktif->contains('id_kelas', $selectedKelas->id_kelas)) {
            $selectedKelas = $kelasJurnalAktif->first();
            $siswaList = Siswa::where('id_kelas', $selectedKelas->id_kelas)->where('is_aktif', 1)->orderBy('nama_siswa')->get();
        }
        if ($jadwalGuruAktif->isEmpty()) {
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

        return view('guru.dashboard', compact(
            'tahunAjaran',
            'kelases',
            'selectedKelas',
            'siswaList', 'izinGuruTerbaru', 'izinGuruHariIni', 'statusKehadiranHariIni', 'jadwalMengajarHariIni', 'namaKelasDiajarHariIni', 'jadwalGuruAktif', 'kelasJurnalAktif',
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
            'laporanTahun',
            'hariIni'
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
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $jadwal = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->whereNull('kelas.deleted_at')
            ->whereNull('mapel.deleted_at')
            ->where('jadwal_mengajar.id_guru', session('auth_guru_id'))
            ->where('jadwal_mengajar.hari', $hariMap[now()->format('l')] ?? '')
            ->whereTime('jam_pelajaran.jam_mulai', '<=', now()->format('H:i:s'))
            ->whereTime('jam_pelajaran.jam_selesai', '>=', now()->format('H:i:s'))
            ->select('jadwal_mengajar.id_jadwal', 'jadwal_mengajar.id_kelas', 'kelas.nama_kelas', 'mapel.nama_mapel', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai')
            ->orderBy('jam_pelajaran.jam_ke')
            ->first();

        return response()->json([
            'status' => 'success',
            'jadwal' => $jadwal,
            'waktu_server' => now()->format('H:i:s'),
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

        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
        $jadwalAktif = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->where('jadwal_mengajar.id_guru', session('auth_guru_id'))
            ->where('jadwal_mengajar.id_kelas', $kelasId)
            ->where('jadwal_mengajar.hari', $hariMap[now()->format('l')] ?? '')
            ->whereTime('jam_pelajaran.jam_mulai', '<=', now()->format('H:i:s'))
            ->whereTime('jam_pelajaran.jam_selesai', '>=', now()->format('H:i:s'))
            ->select('jadwal_mengajar.id_jadwal')
            ->first();

        if (! $jadwalAktif || $tanggal !== now()->toDateString()) {
            return response()->json([
                'status' => 'success',
                'jurnal' => null,
                'siswa' => [],
            ]);
        }

        $jurnal = JurnalKelas::where('id_jadwal', $jadwalAktif->id_jadwal)
            ->whereDate('tanggal', $tanggal)
            ->select('jurnal_kelas.*')
            ->orderByDesc('jurnal_kelas.waktu_input')
            ->orderByDesc('jurnal_kelas.id_jurnal')
            ->first();

        $jurnalIds = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->where('jadwal_mengajar.id_kelas', $kelasId)
            ->whereDate('jurnal_kelas.tanggal', $tanggal)
            ->orderByDesc('jurnal_kelas.id_jurnal')
            ->pluck('jurnal_kelas.id_jurnal');
        $tidakHadir = JurnalSiswaTidakHadir::whereIn('id_jurnal', $jurnalIds)
            ->orderByDesc('id_absen')
            ->get()
            ->groupBy('id_siswa')
            ->map(fn ($items) => $items->first());

        $siswa = Siswa::where('id_kelas', $kelasId)
            ->where('is_aktif', 1)
            ->orderBy('nama_siswa')
            ->get()
            ->map(function ($s) use ($tidakHadir) {
                $th = $tidakHadir->get($s->id_siswa);

                return [
                    'id_siswa' => $s->id_siswa,
                    'nisn' => $s->nisn,
                    'nama_siswa' => $s->nama_siswa,
                    'status' => $th ? $th->status : 'H',
                    'keterangan' => $th ? ($th->keterangan ?? '') : '',
                ];
            });

        return response()->json([
            'status' => 'success',
            'jurnal' => $jurnal ? [
                'id_jurnal' => $jurnal->id_jurnal,
                'materi' => $jurnal->materi,
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
            $validStatuses = ['H', 'S', 'I', 'A'];
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

            $idGuru = session('auth_guru_id');
            $kelasId = (int) $request->id_kelas;
            $tanggal = $request->tanggal;
            if ($tanggal !== now()->toDateString()) {
                DB::rollBack();

                return response()->json(['status' => 'error', 'message' => 'Jurnal hanya dapat diisi untuk hari ini.'], 422);
            }

            $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
            $jadwalAktif = DB::table('jadwal_mengajar')
                ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
                ->whereNull('jadwal_mengajar.deleted_at')
                ->whereNull('jam_pelajaran.deleted_at')
                ->where('jadwal_mengajar.id_guru', $idGuru)
                ->where('jadwal_mengajar.hari', $hariMap[now()->format('l')] ?? '')
                ->whereTime('jam_pelajaran.jam_mulai', '<=', now()->format('H:i:s'))
                ->whereTime('jam_pelajaran.jam_selesai', '>=', now()->format('H:i:s'))
                ->select('jadwal_mengajar.id_jadwal', 'jadwal_mengajar.id_kelas')
                ->orderBy('jam_pelajaran.jam_ke')
                ->first();
            if (! $jadwalAktif || (int) $jadwalAktif->id_kelas !== $kelasId) {
                DB::rollBack();

                return response()->json(['status' => 'error', 'message' => 'Jurnal hanya dapat diisi sesuai jadwal dan jam mengajar yang sedang aktif.'], 422);
            }

            $guruSedangIzin = IzinGuru::where('id_guru', $idGuru)
                ->whereDate('tanggal_izin', $tanggal)
                ->where('status_kepsek', 'disetujui')
                ->where('status_waka', 'disetujui')
                ->exists();
            $statusKehadiranGuru = $guruSedangIzin ? 'Tidak Hadir' : 'Hadir';

            // Cari jurnal yang sudah ada untuk kelas + tanggal ini
            $existing = JurnalKelas::where('id_jadwal', $jadwalAktif->id_jadwal)
                ->whereDate('jurnal_kelas.tanggal', $tanggal)
                ->select('jurnal_kelas.id_jurnal')
                ->orderByDesc('jurnal_kelas.waktu_input')
                ->orderByDesc('jurnal_kelas.id_jurnal')
                ->first();

            if ($existing) {
                // Perbarui jurnal yang sudah ada (ganti data tidak hadir dengan data terbaru)
                $jurnal = JurnalKelas::findOrFail($existing->id_jurnal);
                $jurnal->update([
                    'id_guru' => $idGuru,
                    'status_kehadiran_guru' => $statusKehadiranGuru,
                    'materi' => $request->materi ?? $jurnal->materi,
                    'jumlah_hadir' => $jumlahHadir,
                    'waktu_input' => now(),
                ]);

                JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->delete();
            } else {
                $jurnal = JurnalKelas::create([
                    'id_jadwal' => $jadwalAktif->id_jadwal,
                    'id_guru' => $idGuru,
                    'tanggal' => $tanggal,
                    'status_kehadiran_guru' => $statusKehadiranGuru,
                    'materi' => $request->materi ?? 'Pembelajaran Harian',
                    'jumlah_hadir' => $jumlahHadir,
                    'waktu_input' => now(),
                ]);
            }

            foreach ($tidakHadirList as $th) {
                JurnalSiswaTidakHadir::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_siswa' => $th['id_siswa'],
                    'status' => $th['status'],
                    'keterangan' => $th['keterangan'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Absensi berhasil disimpan!',
                'rekap' => [
                    'hadir' => $jumlahHadir,
                    'sakit' => count(array_filter($tidakHadirList, fn ($x) => $x['status'] === 'S')),
                    'izin' => count(array_filter($tidakHadirList, fn ($x) => $x['status'] === 'I')),
                    'alpa' => count(array_filter($tidakHadirList, fn ($x) => $x['status'] === 'A')),
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
}

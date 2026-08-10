<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Pengaturan;
use App\Models\Mapel;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function guruIndex(Request $request)
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

        return view('guru', compact(
            'tahunAjaran',
            'kelases',
            'selectedKelas',
            'siswaList',
            'guru',
            'namaSekolah',
            'sistemAbsensi'
        ));
    }

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
        $globalRekap = $this->buildAbsensiRekap(null, null, null, false, false);
        $totalSiswa = $globalRekap['total_siswa'];
        $totalHadir = $globalRekap['hadir'];
        $totalSakit = $globalRekap['sakit'];
        $totalIzin  = $globalRekap['izin'];
        $totalAlpa  = $globalRekap['alpa'];
        $pctHadir   = $globalRekap['pct_hadir'];
        $pctSakit   = $globalRekap['pct_sakit'];
        $pctIzin    = $globalRekap['pct_izin'];
        $pctAlpa    = $globalRekap['pct_alpa'];

        $dashboardTren = $this->buildTrenKehadiran(7);
        $kelasPersentase = $this->buildPersentasePerKelas(6);

        // Laporan default: kelas terpilih + bulan berjalan
        $laporanBulan = (int) $request->get('bulan', date('n'));
        $laporanTahun = (int) $request->get('tahun', date('Y'));
        $laporanRekap = $this->buildAbsensiRekap(
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

        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');
        $sistemAbsensi = Pengaturan::get('sistem_absensi', 'Absensi Realtime & Otomatis Rekap');

        return view('welcome', compact(
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
            'sistemAbsensi'
        ));
    }

    public function getLaporanData(Request $request)
    {
        $kelasId = (int) $request->get('kelas_id');
        $bulan   = (int) $request->get('bulan', date('n'));
        $tahun   = (int) $request->get('tahun', date('Y'));
        $filter  = $request->get('data', 'semua');

        if (!$kelasId) {
            return response()->json(['status' => 'error', 'message' => 'Kelas wajib dipilih.'], 422);
        }

        $kelas = Kelas::find($kelasId);
        if (!$kelas) {
            return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan.'], 404);
        }

        $rekap = $this->buildAbsensiRekap($kelasId, $bulan, $tahun);

        if ($filter !== 'semua') {
            $rekap['siswa'] = array_values(array_filter($rekap['siswa'], function ($row) use ($filter) {
                return ($row[$filter] ?? 0) > 0;
            }));
        }

        $rekap['nama_kelas'] = $kelas->nama_kelas;

        return response()->json(['status' => 'success', 'data' => $rekap]);
    }

    /**
     * Agregasi absensi dari jurnal_kelas + jurnal_siswa_tidak_hadir (sumber tunggal).
     */
    protected function buildAbsensiRekap(?int $kelasId = null, ?int $bulan = null, ?int $tahun = null, bool $withCharts = true, bool $withSiswa = true): array
    {
        $siswaQuery = Siswa::where('is_aktif', 1);
        if ($kelasId) {
            $siswaQuery->where('id_kelas', $kelasId);
        }

        if ($withSiswa) {
            $siswaList = $siswaQuery->orderBy('nama_siswa')->get();
            $totalSiswa = $siswaList->count();
        } else {
            $siswaList = collect();
            $totalSiswa = (clone $siswaQuery)->count();
        }

        $jurnalQ = DB::table('jurnal_kelas')
            ->join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->select('jurnal_kelas.id_jurnal', 'jurnal_kelas.tanggal', 'jurnal_kelas.jumlah_hadir', 'jadwal_mengajar.id_kelas');

        if ($kelasId) {
            $jurnalQ->where('jadwal_mengajar.id_kelas', $kelasId);
        }
        if ($bulan) {
            $jurnalQ->whereMonth('jurnal_kelas.tanggal', $bulan);
        }
        if ($tahun) {
            $jurnalQ->whereYear('jurnal_kelas.tanggal', $tahun);
        }

        $jurnals = $jurnalQ->get();
        $jurnalIds = $jurnals->pluck('id_jurnal')->all();
        $jurnalCount = count($jurnalIds);

        $hadir = (int) $jurnals->sum('jumlah_hadir');

        $statusCounts = ['S' => 0, 'I' => 0, 'A' => 0];
        $perSiswaStatus = [];

        if ($jurnalCount > 0) {
            $tidakHadir = JurnalSiswaTidakHadir::whereIn('id_jurnal', $jurnalIds)->get();
            foreach ($tidakHadir as $row) {
                $st = $row->status;
                if (isset($statusCounts[$st])) {
                    $statusCounts[$st]++;
                }
                if ($withSiswa) {
                    if (!isset($perSiswaStatus[$row->id_siswa])) {
                        $perSiswaStatus[$row->id_siswa] = ['S' => 0, 'I' => 0, 'A' => 0];
                    }
                    if (isset($perSiswaStatus[$row->id_siswa][$st])) {
                        $perSiswaStatus[$row->id_siswa][$st]++;
                    }
                }
            }
        }

        $sakit = $statusCounts['S'];
        $izin  = $statusCounts['I'];
        $alpa  = $statusCounts['A'];
        $totalEvents = $hadir + $sakit + $izin + $alpa;

        $pctHadir = $totalEvents > 0 ? (int) round(($hadir / $totalEvents) * 100) : 0;
        $pctSakit = $totalEvents > 0 ? (int) round(($sakit / $totalEvents) * 100) : 0;
        $pctIzin  = $totalEvents > 0 ? (int) round(($izin / $totalEvents) * 100) : 0;
        $pctAlpa  = $totalEvents > 0 ? max(0, 100 - $pctHadir - $pctSakit - $pctIzin) : 0;

        $siswaRekap = [];
        if ($withSiswa) {
            foreach ($siswaList as $s) {
                $st = $perSiswaStatus[$s->id_siswa] ?? ['S' => 0, 'I' => 0, 'A' => 0];
                $sSakit = $st['S'];
                $sIzin  = $st['I'];
                $sAlpa  = $st['A'];
                $sHadir = max(0, $jurnalCount - $sSakit - $sIzin - $sAlpa);
                $pct = $jurnalCount > 0 ? (int) round(($sHadir / $jurnalCount) * 100) : 0;
                $ket = $jurnalCount === 0
                    ? '-'
                    : ($pct >= 90 ? 'Sangat Baik' : ($pct >= 80 ? 'Baik' : ($pct >= 70 ? 'Cukup' : 'Kurang')));

                $siswaRekap[] = [
                    'id_siswa'   => $s->id_siswa,
                    'nama_siswa' => $s->nama_siswa,
                    'nisn'       => $s->nisn,
                    'hadir'      => $sHadir,
                    'sakit'      => $sSakit,
                    'izin'       => $sIzin,
                    'alpa'       => $sAlpa,
                    'persentase' => $pct,
                    'keterangan' => $ket,
                ];
            }
        }

        return [
            'total_siswa'   => $totalSiswa,
            'jurnal_count'  => $jurnalCount,
            'hadir'         => $hadir,
            'sakit'         => $sakit,
            'izin'          => $izin,
            'alpa'          => $alpa,
            'pct_hadir'     => $pctHadir,
            'pct_sakit'     => $pctSakit,
            'pct_izin'      => $pctIzin,
            'pct_alpa'      => $pctAlpa,
            'pct_label'     => $pctHadir >= 90 ? 'Sangat Baik' : ($pctHadir >= 80 ? 'Baik' : ($pctHadir >= 70 ? 'Cukup' : ($totalEvents > 0 ? 'Kurang' : '-'))),
            'siswa'         => $siswaRekap,
            'tren'          => $withCharts ? $this->buildTrenKehadiran(10, $kelasId, $bulan, $tahun) : null,
            'rekap_hari'    => $withCharts ? $this->buildRekapPerHari($kelasId, $bulan, $tahun) : null,
        ];
    }

    protected function buildTrenKehadiran(int $days = 7, ?int $kelasId = null, ?int $bulan = null, ?int $tahun = null): array
    {
        $labels = [];
        $hadir = [];
        $sakit = [];
        $izin = [];
        $alpa = [];
        $pct = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('d/m');

            $q = DB::table('jurnal_kelas')
                ->join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                ->whereDate('jurnal_kelas.tanggal', $date);

            if ($kelasId) {
                $q->where('jadwal_mengajar.id_kelas', $kelasId);
            }
            if ($bulan) {
                $q->whereMonth('jurnal_kelas.tanggal', $bulan);
            }
            if ($tahun) {
                $q->whereYear('jurnal_kelas.tanggal', $tahun);
            }

            $jurnalIds = $q->pluck('jurnal_kelas.id_jurnal');
            $h = (int) DB::table('jurnal_kelas')->whereIn('id_jurnal', $jurnalIds)->sum('jumlah_hadir');
            $s = $jurnalIds->isEmpty() ? 0 : JurnalSiswaTidakHadir::whereIn('id_jurnal', $jurnalIds)->where('status', 'S')->count();
            $iz = $jurnalIds->isEmpty() ? 0 : JurnalSiswaTidakHadir::whereIn('id_jurnal', $jurnalIds)->where('status', 'I')->count();
            $a = $jurnalIds->isEmpty() ? 0 : JurnalSiswaTidakHadir::whereIn('id_jurnal', $jurnalIds)->where('status', 'A')->count();
            $tot = $h + $s + $iz + $a;

            $hadir[] = $h;
            $sakit[] = $s;
            $izin[] = $iz;
            $alpa[] = $a;
            $pct[] = $tot > 0 ? (int) round(($h / $tot) * 100) : 0;
        }

        return compact('labels', 'hadir', 'sakit', 'izin', 'alpa', 'pct');
    }

    protected function buildRekapPerHari(?int $kelasId = null, ?int $bulan = null, ?int $tahun = null): array
    {
        // MySQL: 1=Minggu ... 7=Sabtu → map ke Sen-Sab
        $map = [2 => 'Sen', 3 => 'Sel', 4 => 'Rab', 5 => 'Kam', 6 => 'Jum', 7 => 'Sab'];
        $result = [];
        foreach ($map as $dow => $label) {
            $result[$label] = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];
        }

        $q = DB::table('jurnal_kelas')
            ->join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->select(
                'jurnal_kelas.id_jurnal',
                'jurnal_kelas.jumlah_hadir',
                DB::raw('DAYOFWEEK(jurnal_kelas.tanggal) as dow')
            );

        if ($kelasId) {
            $q->where('jadwal_mengajar.id_kelas', $kelasId);
        }
        if ($bulan) {
            $q->whereMonth('jurnal_kelas.tanggal', $bulan);
        }
        if ($tahun) {
            $q->whereYear('jurnal_kelas.tanggal', $tahun);
        }

        $rows = $q->get();
        if ($rows->isEmpty()) {
            return [
                'labels' => array_values($map),
                'hadir'  => array_fill(0, 6, 0),
                'sakit'  => array_fill(0, 6, 0),
                'izin'   => array_fill(0, 6, 0),
                'alpa'   => array_fill(0, 6, 0),
            ];
        }

        $idsByDow = [];
        foreach ($rows as $row) {
            $dow = (int) $row->dow;
            if (!isset($map[$dow])) {
                continue;
            }
            $label = $map[$dow];
            $result[$label]['hadir'] += (int) $row->jumlah_hadir;
            $idsByDow[$dow][] = $row->id_jurnal;
        }

        foreach ($idsByDow as $dow => $ids) {
            $label = $map[$dow];
            $counts = JurnalSiswaTidakHadir::whereIn('id_jurnal', $ids)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');
            $result[$label]['sakit'] = (int) ($counts['S'] ?? 0);
            $result[$label]['izin']  = (int) ($counts['I'] ?? 0);
            $result[$label]['alpa']  = (int) ($counts['A'] ?? 0);
        }

        return [
            'labels' => array_values($map),
            'hadir'  => array_map(fn ($l) => $result[$l]['hadir'], array_values($map)),
            'sakit'  => array_map(fn ($l) => $result[$l]['sakit'], array_values($map)),
            'izin'   => array_map(fn ($l) => $result[$l]['izin'], array_values($map)),
            'alpa'   => array_map(fn ($l) => $result[$l]['alpa'], array_values($map)),
        ];
    }

    protected function buildPersentasePerKelas(int $limit = 6): array
    {
        $kelases = Kelas::withCount(['siswa' => fn ($q) => $q->where('is_aktif', 1)])
            ->orderBy('nama_kelas')
            ->get();

        $items = [];
        foreach ($kelases as $kelas) {
            $rekap = $this->buildAbsensiRekap((int) $kelas->id_kelas, null, null, false, false);
            $total = $rekap['hadir'] + $rekap['sakit'] + $rekap['izin'] + $rekap['alpa'];
            if ($total === 0 && $rekap['total_siswa'] === 0) {
                continue;
            }
            $items[] = [
                'id_kelas'   => $kelas->id_kelas,
                'nama_kelas' => $kelas->nama_kelas,
                'persentase' => $rekap['pct_hadir'],
                'total_siswa'=> $rekap['total_siswa'],
            ];
        }

        usort($items, fn ($a, $b) => $b['persentase'] <=> $a['persentase']);

        return array_slice($items, 0, $limit);
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

    public function storeSiswa(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required',
            'nisn' => 'required',
            'id_kelas' => 'required',
            'jenis_kelamin' => 'required'
        ]);

        $siswa = Siswa::create([
            'nama_siswa' => $request->nama_siswa,
            'nisn' => $request->nisn,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'is_aktif' => 1
        ]);

        return response()->json(['status' => 'success', 'message' => 'Siswa berhasil ditambahkan!', 'data' => $siswa]);
    }

    public function storeKelas(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'tingkat_kelas' => 'required',
            'jurusan' => 'required'
        ]);

        $tahun = TahunAjaran::first()?->id_tahun_ajaran ?? 1;

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tingkat_kelas' => $request->tingkat_kelas,
            'jurusan' => $request->jurusan,
            'id_tahun_ajaran' => $tahun
        ]);

        return response()->json(['status' => 'success', 'message' => 'Kelas berhasil ditambahkan!', 'data' => $kelas]);
    }

    public function storeMapel(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'nullable|string|max:20|unique:mapel,kode_mapel',
        ]);

        $mapel = Mapel::create([
            'kode_mapel' => $request->kode_mapel ?: null,
            'nama_mapel' => $request->nama_mapel,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Mata pelajaran berhasil ditambahkan!',
            'data'    => $mapel,
        ]);
    }

    public function destroyMapel($id)
    {
        $mapel = Mapel::findOrFail($id);
        $jadwalCount = $mapel->jadwal()->count();

        if ($jadwalCount > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => "Mapel tidak dapat dihapus karena masih dipakai di {$jadwalCount} jadwal mengajar.",
            ], 422);
        }

        $mapel->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Mata pelajaran berhasil dihapus!',
        ]);
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required',
            'no_hp' => 'nullable'
        ]);

        $guru = Guru::where('Peran', 'Wali Kelas')->first() ?? Guru::first();
        if ($guru) {
            $guru->update([
                'nama_guru' => $request->nama_guru,
                'no_hp' => $request->no_hp
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Profil berhasil diperbarui!']);
    }

    public function storeGuru(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required|string',
            'username'  => 'required|string|unique:guru,username',
            'password'  => 'required|string|min:4',
        ], [
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan guru lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 4 karakter.',
        ]);

        $guru = Guru::create([
            'nip'           => $request->nip ?: null,
            'nama_guru'     => $request->nama_guru,
            'Peran'         => $request->peran ?? 'Guru',
            'no_hp'         => $request->no_hp ?: null,
            'username'      => strtolower(trim($request->username)),
            'password_hash' => \Illuminate\Support\Facades\Hash::make($request->password),
            'is_admin'      => $request->is_admin ? 1 : 0,
            'is_aktif'      => 1,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Guru berhasil ditambahkan!',
            'data'    => $guru
        ]);
    }

    public function updatePengaturan(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'semester'     => 'required|string',
        ], [
            'nama_sekolah.required' => 'Nama sekolah tidak boleh kosong.',
            'tahun_ajaran.required' => 'Tahun ajaran tidak boleh kosong.',
            'semester.required'     => 'Semester tidak boleh kosong.',
        ]);

        Pengaturan::set('nama_sekolah', trim($request->nama_sekolah));
        if ($request->filled('sistem_absensi')) {
            Pengaturan::set('sistem_absensi', trim($request->sistem_absensi));
        }

        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        if ($tahun) {
            $tahun->update([
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester'     => trim($request->semester),
            ]);
        } else {
            TahunAjaran::create([
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester'     => trim($request->semester),
                'is_aktif'     => 1,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengaturan sistem berhasil diperbarui!',
            'data'    => [
                'nama_sekolah' => trim($request->nama_sekolah),
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester'     => trim($request->semester),
                'sistem_absensi' => trim($request->sistem_absensi ?? Pengaturan::get('sistem_absensi')),
            ]
        ]);
    }

    public function destroySiswa($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data siswa berhasil dihapus!'
        ]);
    }

    public function destroyKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kelas berhasil dihapus!'
        ]);
    }

    public function destroyGuru($id)
    {
        $guru = Guru::findOrFail($id);

        if ($guru->id_guru == session('auth_guru_id')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri!'
            ], 422);
        }

        $guru->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data guru berhasil dihapus!'
        ]);
    }
}


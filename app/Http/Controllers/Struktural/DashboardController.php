<?php

namespace App\Http\Controllers\Struktural;

use App\Http\Controllers\Controller;
use App\Models\AkunSatpam;
use App\Models\DispenSiswa;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\IzinGuru;
use App\Models\JamPelajaran;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Kelas;
use App\Models\Pengaturan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(protected AbsensiService $absensiService) {}

    public function index(Request $request)
    {
        $isSatpam = session('auth_role') === 'satpam';
        $guru = $isSatpam
            ? (AkunSatpam::find(session('auth_satpam_id')) ?? AkunSatpam::first())
            : (Guru::find(session('auth_guru_id')) ?? Guru::first());

        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $sidebar = 'partials.sidebar_struktural';
        $isWaliKelas = session('auth_role') === 'walikelas';
        $profilUpdateUrl = route('struktural.profil.update');
        $isGuruPiket = session('auth_role') === 'guru_piket';
        $guruAktif = Guru::where('is_admin', 0)->where('is_aktif', 1)->orderBy('nama_guru')->get();
        $siswaAktif = Siswa::with('kelas')->where('is_aktif', 1)->orderBy('nama_siswa')->get();
        if ($isWaliKelas && session('auth_kelas_id')) {
            $kelasesWali = Kelas::where('id_kelas', session('auth_kelas_id'))->get();
        } else {
            $kelasesWali = collect();
        }
        $kelases = $kelasesWali;
        $selectedKelas = $kelasesWali->first() ?? Kelas::first();
        $laporanBulan = (int) $request->get('bulan', date('n'));
        $laporanTahun = (int) $request->get('tahun', date('Y'));
        $laporanRekap = $selectedKelas
            ? $this->absensiService->buildAbsensiRekap((int) $selectedKelas->id_kelas, $laporanBulan, $laporanTahun)
            : $this->absensiService->buildAbsensiRekap(null, $laporanBulan, $laporanTahun);
        $dispenTerbaru = DispenSiswa::with(['siswa.kelas', 'guruPiket'])
            ->where('id_guru_piket', session('auth_guru_id'))
            ->where('jenis_absen', 'D')
            ->latest()->limit(25)->get();
        $absensiSiswaTerbaru = DispenSiswa::with(['siswa.kelas'])
            ->where('id_guru_piket', session('auth_guru_id'))
            ->whereIn('jenis_absen', ['S', 'I'])
            ->latest()->limit(25)->get();
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'urutan')->toArray();
        $hariIni = $hariMap[now()->dayOfWeekIso];
        $jadwalHariIni = DB::table('jadwal_mengajar')
            ->join('guru', 'jadwal_mengajar.id_guru', '=', 'guru.id_guru')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('guru.deleted_at')
            ->whereNull('mapel.deleted_at')
            ->whereNull('kelas.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->where('jadwal_mengajar.hari', $hariIni)
            ->select('jadwal_mengajar.id_jadwal', 'jadwal_mengajar.hari', 'guru.nama_guru', 'mapel.nama_mapel', 'kelas.nama_kelas', 'jam_pelajaran.jam_ke', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai')
            ->orderBy('jam_pelajaran.jam_ke')
            ->orderBy('kelas.nama_kelas')
            ->get();
        $totalJadwalHariIni = $jadwalHariIni->count();
        $totalKelasHariIni = $jadwalHariIni->pluck('nama_kelas')->unique()->count();
        $totalGuruHariIni = $jadwalHariIni->pluck('nama_guru')->unique()->count();
        $jamAktif = JamPelajaran::where('jam_mulai', '<=', now()->format('H:i:s'))
            ->where('jam_selesai', '>=', now()->format('H:i:s'))
            ->whereIn('jam_ke', $jadwalHariIni->pluck('jam_ke'))
            ->orderBy('jam_ke')
            ->first();
        $izinGuruTerbaru = collect();
        if (session('auth_role') === 'guru_piket') {
            $izinGuruTerbaru = IzinGuru::with('guru')
                ->where('id_guru_piket', session('auth_guru_id'))
                ->latest()
                ->limit(10)
                ->get();
        }

        $dispenHariIni = collect();
        $satpamDispenRiwayat = collect();
        $satpamTanggal = $request->get('satpam_tanggal', now()->toDateString());
        $isSatpam = session('auth_role') === 'satpam';
        if ($isSatpam) {
            $dispenHariIni = DispenSiswa::with(['siswa.kelas', 'guruPiket'])
                ->whereDate('tanggal_dispen', now()->toDateString())
                ->latest()
                ->get();

            $satpamDispenRiwayat = DispenSiswa::with(['siswa.kelas', 'guruPiket'])
                ->whereDate('tanggal_dispen', $satpamTanggal)
                ->latest()
                ->get();
        }

        // ── Waka SDM: data kehadiran mengajar guru ──
        $isWakaSDM = session('auth_role') === 'waka_sdm';
        $sdmTanggal = $request->get('tanggal', now()->toDateString());
        $sdmBulan = (int) $request->get('sdm_bulan', date('n'));
        $sdmTahun = (int) $request->get('sdm_tahun', date('Y'));
        $sdmJadwal = collect();
        $sdmIzinGuru = collect();
        $sdmRekapGuru = [];
        $sdmStatHadir = 0;
        $sdmStatTidakHadir = 0;
        $sdmStatBelumIsi = 0;

        if ($isWakaSDM) {
            $hariFilter = $hariMap[Carbon::parse($sdmTanggal)->dayOfWeekIso] ?? null;

            if ($hariFilter) {
                $sdmJadwal = DB::table('jadwal_mengajar')
                    ->join('guru', 'jadwal_mengajar.id_guru', '=', 'guru.id_guru')
                    ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
                    ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
                    ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
                    ->whereNull('jadwal_mengajar.deleted_at')
                    ->whereNull('guru.deleted_at')
                    ->whereNull('mapel.deleted_at')
                    ->whereNull('kelas.deleted_at')
                    ->whereNull('jam_pelajaran.deleted_at')
                    ->where('jadwal_mengajar.hari', $hariFilter)
                    ->select(
                        'jadwal_mengajar.id_jadwal',
                        'jadwal_mengajar.id_guru',
                        'guru.nama_guru',
                        'mapel.nama_mapel',
                        'kelas.nama_kelas',
                        'jam_pelajaran.jam_ke',
                        'jam_pelajaran.jam_mulai',
                        'jam_pelajaran.jam_selesai'
                    )
                    ->orderBy('jam_pelajaran.jam_ke')
                    ->orderBy('kelas.nama_kelas')
                    ->get();

                // Map jurnal ke jadwal
                $jurnalMap = JurnalKelas::whereDate('tanggal', $sdmTanggal)
                    ->get()
                    ->keyBy('id_jadwal');

                $sdmJadwal = $sdmJadwal->map(function ($item) use ($jurnalMap) {
                    $jurnal = $jurnalMap[$item->id_jadwal] ?? null;
                    $item->status_jurnal = $jurnal ? $jurnal->status_kehadiran_guru : null;
                    $item->materi = $jurnal->materi ?? null;
                    $item->waktu_input = $jurnal->waktu_input ?? null;

                    return $item;
                });

                $sdmStatHadir = $sdmJadwal->where('status_jurnal', 'Hadir')->count();
                $sdmStatTidakHadir = $sdmJadwal->where('status_jurnal', 'Tidak Hadir')->count();
                $sdmStatBelumIsi = $sdmJadwal->whereNull('status_jurnal')->count();
            }

            $sdmIzinGuru = IzinGuru::with('guru')
                ->latest()
                ->limit(20)
                ->get();

            $sdmRekapGuru = $this->buildRekapBulananGuru($sdmBulan, $sdmTahun);
        }

        // ── Wali Kelas: Data Kelas yang Diampu ──
        $waliKelasObj = null;
        $waliKelasId = session('auth_kelas_id');
        if (! $waliKelasId && $isWaliKelas) {
            $waliKelasObj = Kelas::where('id_wali_kelas', session('auth_guru_id'))->first();
            if ($waliKelasObj) {
                $waliKelasId = $waliKelasObj->id_kelas;
                session([
                    'auth_kelas_id' => $waliKelasObj->id_kelas,
                    'auth_nama_kelas' => $waliKelasObj->nama_kelas,
                ]);
            }
        } elseif ($waliKelasId) {
            $waliKelasObj = Kelas::find($waliKelasId);
        }

        $waliSiswaList = collect();
        $waliBulan = (int) $request->get('wali_bulan', date('n'));
        $waliTahun = (int) $request->get('wali_tahun', date('Y'));
        $waliRekapData = [];
        $dispenDanIzinKelas = collect();
        $siswaPerluPerhatian = [];

        if ($isWaliKelas && $waliKelasId) {
            $waliSiswaList = Siswa::where('id_kelas', $waliKelasId)->where('is_aktif', 1)->orderBy('nama_siswa')->get();
            $waliRekapData = $this->absensiService->buildAbsensiRekap($waliKelasId, $waliBulan, $waliTahun);
            $dispenDanIzinKelas = DispenSiswa::with('siswa')
                ->whereHas('siswa', fn ($q) => $q->where('id_kelas', $waliKelasId))
                ->latest()
                ->limit(30)
                ->get();

            $siswaPerluPerhatian = array_values(array_filter($waliRekapData['siswa'] ?? [], function ($row) {
                return ($row['alpa'] ?? 0) >= 3 || (($row['sakit'] ?? 0) + ($row['izin'] ?? 0)) >= 5;
            }));

            // 1. Absensi Harian Kelas Hari Ini (Real Time)
            $waliTanggalHariIni = now()->toDateString();
            $jurnalHariIniIds = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                ->where('jadwal_mengajar.id_kelas', $waliKelasId)
                ->whereDate('jurnal_kelas.tanggal', $waliTanggalHariIni)
                ->pluck('jurnal_kelas.id_jurnal');

            $tidakHadirHariIni = JurnalSiswaTidakHadir::whereIn('id_jurnal', $jurnalHariIniIds)
                ->get()
                ->keyBy('id_siswa');

            $dispenHariIniClass = DispenSiswa::whereHas('siswa', fn($q) => $q->where('id_kelas', $waliKelasId))
                ->whereDate('tanggal_dispen', $waliTanggalHariIni)
                ->get()
                ->keyBy('id_siswa');

            $hadirCount = 0; $sakitCount = 0; $izinCount = 0; $alpaCount = 0; $dispenCount = 0;

            $waliAbsensiHariIniList = $waliSiswaList->map(function ($s) use ($tidakHadirHariIni, $dispenHariIniClass, &$hadirCount, &$sakitCount, &$izinCount, &$alpaCount, &$dispenCount) {
                $th = $tidakHadirHariIni->get($s->id_siswa);
                $ds = $dispenHariIniClass->get($s->id_siswa);

                $status = 'H';
                $ket = '';

                if ($ds) {
                    $status = $ds->jenis_absen;
                    $ket = $ds->alasan ?? 'Dispensasi/Izin';
                } elseif ($th) {
                    $status = $th->status;
                    $ket = $th->keterangan ?? '';
                }

                if ($status === 'H') $hadirCount++;
                elseif ($status === 'S') $sakitCount++;
                elseif ($status === 'I') $izinCount++;
                elseif ($status === 'A') $alpaCount++;
                elseif ($status === 'D') $dispenCount++;

                return [
                    'id_siswa' => $s->id_siswa,
                    'nisn' => $s->nisn ?? '-',
                    'nama_siswa' => $s->nama_siswa,
                    'jenis_kelamin' => $s->jenis_kelamin ?? 'L',
                    'no_hp_ortu' => $s->no_hp_ortu ?? '',
                    'status' => $status,
                    'keterangan' => $ket,
                ];
            });

            $totalSiswaWali = $waliSiswaList->count();
            $pctHadirHariIni = $totalSiswaWali > 0 ? (int) round(($hadirCount / $totalSiswaWali) * 100) : 0;
            $waliStatsHariIni = [
                'total_siswa' => $totalSiswaWali,
                'hadir' => $hadirCount,
                'sakit' => $sakitCount,
                'izin' => $izinCount,
                'alpa' => $alpaCount,
                'dispensasi' => $dispenCount,
                'pct_hadir' => $pctHadirHariIni,
            ];

            // 2. Jurnal Harian Real-Time Hari Ini
            $hariNamaIndo = Hari::getActiveDays()->pluck('nama_hari', 'urutan')->toArray()[$hariIni] ?? $hariIni;
            $jadwalWaliHariIni = DB::table('jadwal_mengajar')
                ->join('guru', 'jadwal_mengajar.id_guru', '=', 'guru.id_guru')
                ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
                ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
                ->whereNull('jadwal_mengajar.deleted_at')
                ->whereNull('guru.deleted_at')
                ->whereNull('mapel.deleted_at')
                ->whereNull('jam_pelajaran.deleted_at')
                ->where('jadwal_mengajar.id_kelas', $waliKelasId)
                ->where('jadwal_mengajar.hari', $hariNamaIndo)
                ->select(
                    'jadwal_mengajar.id_jadwal',
                    'guru.nama_guru',
                    'guru.foto_profil',
                    'mapel.nama_mapel',
                    'jam_pelajaran.jam_ke',
                    'jam_pelajaran.jam_mulai',
                    'jam_pelajaran.jam_selesai'
                )
                ->orderBy('jam_pelajaran.jam_ke')
                ->get();

            $jurnalWaliHariIniMap = JurnalKelas::whereDate('tanggal', $waliTanggalHariIni)
                ->whereIn('id_jadwal', $jadwalWaliHariIni->pluck('id_jadwal'))
                ->get()
                ->keyBy('id_jadwal');

            $jamSekarang = now()->format('H:i:s');
            $jurnalTerisiCount = 0;
            $waliJadwalHariIni = $jadwalWaliHariIni->map(function ($j) use ($jurnalWaliHariIniMap, $jamSekarang, &$jurnalTerisiCount) {
                $jurnal = $jurnalWaliHariIniMap->get($j->id_jadwal);
                if ($jurnal) {
                    $jurnalTerisiCount++;
                }

                $statusPembelajaran = 'Belum Dimulai';
                if ($jurnal) {
                    $statusPembelajaran = 'Jurnal Terisi';
                } elseif ($jamSekarang >= $j->jam_mulai && $jamSekarang <= $j->jam_selesai) {
                    $statusPembelajaran = 'Sedang Berlangsung';
                } elseif ($jamSekarang > $j->jam_selesai) {
                    $statusPembelajaran = 'Selesai (Belum Isi Jurnal)';
                }

                $j->jurnal = $jurnal;
                $j->status_pembelajaran = $statusPembelajaran;
                return $j;
            });

            $totalJamHariIni = $jadwalWaliHariIni->count();
            $waliStatsJurnalHariIni = [
                'total_jam' => $totalJamHariIni,
                'terisi' => $jurnalTerisiCount,
                'guru_count' => $jadwalWaliHariIni->pluck('nama_guru')->unique()->count(),
                'pct_terisi' => $totalJamHariIni > 0 ? (int) round(($jurnalTerisiCount / $totalJamHariIni) * 100) : 0,
            ];

            // 3. Rekap Absensi Kelas (Filter Tanggal Bebas)
            $waliTglMulaiAbsen = $request->get('wali_tgl_mulai', date('Y-m-01'));
            $waliTglSelesaiAbsen = $request->get('wali_tgl_selesai', date('Y-m-d'));
            $waliRekapAbsensiRange = $this->absensiService->buildAbsensiRekapRange($waliKelasId, $waliTglMulaiAbsen, $waliTglSelesaiAbsen, true);

            // 4. Rekap Jurnal Pembelajaran Kelas (Filter Tanggal Bebas)
            $waliTglMulaiJurnal = $request->get('jurnal_tgl_mulai', date('Y-01-01'));
            $waliTglSelesaiJurnal = $request->get('jurnal_tgl_selesai', date('Y-m-d'));

            $jurnalQ = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                ->join('guru', 'jurnal_kelas.id_guru', '=', 'guru.id_guru')
                ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
                ->where('jadwal_mengajar.id_kelas', $waliKelasId);

            if ($waliTglMulaiJurnal) {
                $jurnalQ->whereDate('jurnal_kelas.tanggal', '>=', $waliTglMulaiJurnal);
            }
            if ($waliTglSelesaiJurnal) {
                $jurnalQ->whereDate('jurnal_kelas.tanggal', '<=', $waliTglSelesaiJurnal);
            }

            $waliRekapJurnalList = $jurnalQ->orderByDesc('jurnal_kelas.tanggal')
                ->orderByDesc('jurnal_kelas.waktu_input')
                ->select(
                    'jurnal_kelas.*',
                    'guru.nama_guru',
                    'mapel.nama_mapel'
                )
                ->get();
        } else {
            $waliTanggalHariIni = now()->toDateString();
            $waliAbsensiHariIniList = collect();
            $waliStatsHariIni = ['total_siswa' => 0, 'hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'dispensasi' => 0, 'pct_hadir' => 0];
            $waliJadwalHariIni = collect();
            $waliStatsJurnalHariIni = ['total_jam' => 0, 'terisi' => 0, 'guru_count' => 0, 'pct_terisi' => 0];
            $waliTglMulaiAbsen = date('Y-m-01');
            $waliTglSelesaiAbsen = date('Y-m-d');
            $waliRekapAbsensiRange = [];
            $waliTglMulaiJurnal = date('Y-01-01');
            $waliTglSelesaiJurnal = date('Y-m-d');
            $waliRekapJurnalList = collect();
        }

        return view('struktural.dashboard', compact(
            'guru',
            'namaSekolah',
            'tahunAjaran',
            'sidebar',
            'profilUpdateUrl',
            'hariIni',
            'jadwalHariIni',
            'totalJadwalHariIni',
            'totalKelasHariIni',
            'totalGuruHariIni',
            'jamAktif',
            'guruAktif', 'izinGuruTerbaru', 'isGuruPiket', 'isWaliKelas', 'kelasesWali', 'kelases', 'selectedKelas', 'laporanBulan', 'laporanTahun', 'laporanRekap', 'siswaAktif', 'dispenTerbaru', 'absensiSiswaTerbaru', 'isSatpam', 'dispenHariIni',
            'isWakaSDM', 'sdmTanggal', 'sdmJadwal', 'sdmIzinGuru', 'sdmStatHadir', 'sdmStatTidakHadir', 'sdmStatBelumIsi', 'sdmBulan', 'sdmTahun', 'sdmRekapGuru',
            'waliKelasObj', 'waliKelasId', 'waliSiswaList', 'waliBulan', 'waliTahun', 'waliRekapData', 'dispenDanIzinKelas', 'siswaPerluPerhatian',
            'waliTanggalHariIni', 'waliAbsensiHariIniList', 'waliStatsHariIni', 'waliJadwalHariIni', 'waliStatsJurnalHariIni',
            'waliTglMulaiAbsen', 'waliTglSelesaiAbsen', 'waliRekapAbsensiRange',
            'waliTglMulaiJurnal', 'waliTglSelesaiJurnal', 'waliRekapJurnalList',
            'satpamTanggal', 'satpamDispenRiwayat'
        ));
    }

    public function izinkanKeluar(DispenSiswa $dispen)
    {
        abort_unless(session('auth_role') === 'satpam', 403);

        $dispen->update([
            'waktu_keluar' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Siswa berhasil diizinkan keluar sekolah.',
            'waktu_keluar' => now()->format('H:i:s'),
        ]);
    }

    public function izinkanMasuk(DispenSiswa $dispen)
    {
        abort_unless(session('auth_role') === 'satpam', 403);

        $dispen->update([
            'waktu_masuk' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Siswa berhasil diizinkan masuk kembali ke sekolah.',
            'waktu_masuk' => now()->format('H:i:s'),
        ]);
    }

    private function buildRekapBulananGuru(int $bulan, int $tahun): array
    {
        $allGuru = Guru::where('is_admin', 0)->where('is_aktif', 1)->orderBy('nama_guru')->get();
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'urutan')->toArray();

        $jadwals = DB::table('jadwal_mengajar')
            ->whereNull('deleted_at')
            ->get();

        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        $dayCounts = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($tahun, $bulan, $d);
            $hariNama = $hariMap[$date->dayOfWeekIso] ?? null;
            if ($hariNama) {
                $dayCounts[$hariNama] = ($dayCounts[$hariNama] ?? 0) + 1;
            }
        }

        $jurnals = JurnalKelas::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $rekap = [];
        foreach ($allGuru as $g) {
            $guruJadwals = $jadwals->where('id_guru', $g->id_guru);

            $totalSesi = 0;
            foreach ($guruJadwals as $j) {
                $totalSesi += ($dayCounts[$j->hari] ?? 0);
            }

            $guruJurnals = $jurnals->where('id_guru', $g->id_guru);
            $hadir = $guruJurnals->where('status_kehadiran_guru', 'Hadir')->count();
            $tidakHadir = $guruJurnals->where('status_kehadiran_guru', 'Tidak Hadir')->count();
            $belumIsi = max(0, $totalSesi - ($hadir + $tidakHadir));

            $persentase = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 1) : 100;

            $rekap[] = [
                'id_guru' => $g->id_guru,
                'nip' => $g->nip ?? '-',
                'nama_guru' => $g->nama_guru,
                'username' => $g->username,
                'total_sesi' => $totalSesi,
                'hadir' => $hadir,
                'tidak_hadir' => $tidakHadir,
                'belum_isi' => $belumIsi,
                'persentase' => $persentase,
            ];
        }

        return $rekap;
    }

    public function exportRekapGuru(Request $request)
    {
        abort_unless(session('auth_role') === 'waka_sdm', 403);

        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));
        $monthsMap = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $namaBulan = $monthsMap[$bulan] ?? 'Bulan_'.$bulan;

        $rekap = $this->buildRekapBulananGuru($bulan, $tahun);

        $filename = "rekap_presensi_guru_{$namaBulan}_{$tahun}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rekap, $namaBulan, $tahun) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['REKAPITULASI KEHADIRAN MENGAJAR GURU']);
            fputcsv($file, ["Periode: {$namaBulan} {$tahun}"]);
            fputcsv($file, []);

            fputcsv($file, [
                'No',
                'NIP',
                'Nama Guru',
                'Username',
                'Total Sesi Mengajar',
                'Jumlah Sesi Hadir',
                'Jumlah Sesi Tidak Hadir / Izin',
                'Sesi Belum Isi Jurnal',
                'Persentase Kehadiran (%)',
            ]);

            foreach ($rekap as $idx => $row) {
                fputcsv($file, [
                    $idx + 1,
                    $row['nip'],
                    $row['nama_guru'],
                    $row['username'],
                    $row['total_sesi'],
                    $row['hadir'],
                    $row['tidak_hadir'],
                    $row['belum_isi'],
                    $row['persentase'].'%',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRekapKelasWali(Request $request)
    {
        abort_unless(session('auth_role') === 'walikelas', 403);

        $kelasId = session('auth_kelas_id');
        if (! $kelasId) {
            $guruId = session('auth_guru_id');
            $kelasObj = Kelas::where('id_wali_kelas', $guruId)->first();
            $kelasId = $kelasObj?->id_kelas;
        }

        abort_unless($kelasId, 404, 'Kelas tidak ditemukan.');

        $kelas = Kelas::find($kelasId);
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));

        $monthsMap = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $namaBulan = $monthsMap[$bulan] ?? 'Bulan_'.$bulan;

        $rekap = $this->absensiService->buildAbsensiRekap($kelasId, $bulan, $tahun);

        $namaKelas = str_replace(' ', '_', $kelas->nama_kelas ?? 'Kelas');
        $filename = "rekap_absensi_{$namaKelas}_{$namaBulan}_{$tahun}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rekap, $kelas, $namaBulan, $tahun) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['REKAPITULASI ABSENSI SISWA KELAS '.strtoupper($kelas->nama_kelas)]);
            fputcsv($file, ["Periode: {$namaBulan} {$tahun}"]);
            fputcsv($file, []);

            fputcsv($file, [
                'No',
                'NISN',
                'Nama Siswa',
                'Jenis Kelamin',
                'No. HP Ortu',
                'Hadir (H)',
                'Sakit (S)',
                'Izin (I)',
                'Alpa (A)',
                'Persentase Kehadiran (%)',
                'Keterangan',
            ]);

            foreach ($rekap['siswa'] ?? [] as $idx => $row) {
                fputcsv($file, [
                    $idx + 1,
                    $row['nisn'] ?? '-',
                    $row['nama_siswa'] ?? '-',
                    $row['jenis_kelamin'] ?? '-',
                    $row['no_hp_ortu'] ?? '-',
                    $row['hadir'] ?? 0,
                    $row['sakit'] ?? 0,
                    $row['izin'] ?? 0,
                    $row['alpa'] ?? 0,
                    ($row['persentase'] ?? 0).'%',
                    $row['keterangan'] ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRekapAbsensiWali(Request $request)
    {
        abort_unless(session('auth_role') === 'walikelas', 403);

        $kelasId = session('auth_kelas_id');
        if (! $kelasId) {
            $guruId = session('auth_guru_id');
            $kelasObj = Kelas::where('id_wali_kelas', $guruId)->first();
            $kelasId = $kelasObj?->id_kelas;
        }

        abort_unless($kelasId, 404, 'Kelas tidak ditemukan.');

        $kelas = Kelas::find($kelasId);
        $tglMulai = $request->get('tgl_mulai', date('Y-m-01'));
        $tglSelesai = $request->get('tgl_selesai', date('Y-m-d'));

        $rekap = $this->absensiService->buildAbsensiRekapRange($kelasId, $tglMulai, $tglSelesai, true);

        $namaKelas = str_replace(' ', '_', $kelas->nama_kelas ?? 'Kelas');
        $filename = "rekap_absensi_{$namaKelas}_{$tglMulai}_sd_{$tglSelesai}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rekap, $kelas, $tglMulai, $tglSelesai) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['REKAPITULASI ABSENSI SISWA KELAS '.strtoupper($kelas->nama_kelas)]);
            fputcsv($file, ["Periode Tanggal: {$tglMulai} s/d {$tglSelesai}"]);
            fputcsv($file, []);

            fputcsv($file, [
                'No',
                'NISN',
                'Nama Siswa',
                'Jenis Kelamin',
                'No. HP Ortu',
                'Hadir (H)',
                'Sakit (S)',
                'Izin (I)',
                'Alpa (A)',
                'Persentase Kehadiran (%)',
                'Keterangan Performa',
            ]);

            foreach ($rekap['siswa'] ?? [] as $idx => $row) {
                fputcsv($file, [
                    $idx + 1,
                    $row['nisn'] ?? '-',
                    $row['nama_siswa'] ?? '-',
                    $row['jenis_kelamin'] ?? '-',
                    $row['no_hp_ortu'] ?? '-',
                    $row['hadir'] ?? 0,
                    $row['sakit'] ?? 0,
                    $row['izin'] ?? 0,
                    $row['alpa'] ?? 0,
                    ($row['persentase'] ?? 0).'%',
                    $row['keterangan'] ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function simpanAbsensiHarianWali(Request $request)
    {
        abort_unless(session('auth_role') === 'walikelas', 403);

        $request->validate([
            'absensi' => 'required|array',
        ]);

        $kelasId = session('auth_kelas_id');
        if (! $kelasId) {
            $guruId = session('auth_guru_id');
            $kelasObj = Kelas::where('id_wali_kelas', $guruId)->first();
            $kelasId = $kelasObj?->id_kelas;
        }

        abort_unless($kelasId, 404, 'Kelas wali tidak ditemukan.');

        DB::beginTransaction();
        try {
            $tanggal = now()->toDateString();
            $idGuru = session('auth_guru_id');

            $jadwal = DB::table('jadwal_mengajar')
                ->where('id_kelas', $kelasId)
                ->whereNull('deleted_at')
                ->first();

            $idJadwal = $jadwal ? $jadwal->id_jadwal : null;

            if ($idJadwal) {
                $jurnal = JurnalKelas::where('id_jadwal', $idJadwal)
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                $jumlahHadir = 0;
                $tidakHadirList = [];

                foreach ($request->absensi as $idSiswa => $item) {
                    $st = $item['status'] ?? 'H';
                    $ket = $item['keterangan'] ?? '';

                    if ($st === 'H') {
                        $jumlahHadir++;
                    } else {
                        $tidakHadirList[] = [
                            'id_siswa' => $idSiswa,
                            'status' => $st,
                            'keterangan' => $ket,
                        ];
                    }
                }

                if ($jurnal) {
                    $jurnal->update([
                        'jumlah_hadir' => $jumlahHadir,
                        'waktu_input' => now(),
                    ]);
                    JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->delete();
                } else {
                    $jurnal = JurnalKelas::create([
                        'id_jadwal' => $idJadwal,
                        'id_guru' => $idGuru,
                        'tanggal' => $tanggal,
                        'status_kehadiran_guru' => 'Hadir',
                        'materi' => 'Presensi Harian Wali Kelas',
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
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Presensi harian kelas berhasil disimpan!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan presensi harian: '.$e->getMessage(),
            ], 500);
        }
    }

    public function exportRekapJurnalWali(Request $request)
    {
        abort_unless(session('auth_role') === 'walikelas', 403);

        $kelasId = session('auth_kelas_id');
        if (! $kelasId) {
            $guruId = session('auth_guru_id');
            $kelasObj = Kelas::where('id_wali_kelas', $guruId)->first();
            $kelasId = $kelasObj?->id_kelas;
        }

        abort_unless($kelasId, 404, 'Kelas tidak ditemukan.');

        $kelas = Kelas::find($kelasId);
        $tglMulai = $request->get('jurnal_tgl_mulai', date('Y-01-01'));
        $tglSelesai = $request->get('jurnal_tgl_selesai', date('Y-m-d'));

        $jurnals = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->join('guru', 'jurnal_kelas.id_guru', '=', 'guru.id_guru')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->where('jadwal_mengajar.id_kelas', $kelasId)
            ->whereBetween('jurnal_kelas.tanggal', [$tglMulai, $tglSelesai])
            ->orderByDesc('jurnal_kelas.tanggal')
            ->orderByDesc('jurnal_kelas.waktu_input')
            ->select('jurnal_kelas.*', 'guru.nama_guru', 'mapel.nama_mapel')
            ->get();

        $namaKelas = str_replace(' ', '_', $kelas->nama_kelas ?? 'Kelas');
        $filename = "rekap_jurnal_{$namaKelas}_{$tglMulai}_sd_{$tglSelesai}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($jurnals, $kelas, $tglMulai, $tglSelesai) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['REKAPITULASI JURNAL PEMBELAJARAN KELAS '.strtoupper($kelas->nama_kelas)]);
            fputcsv($file, ["Periode Tanggal: {$tglMulai} s/d {$tglSelesai}"]);
            fputcsv($file, []);

            fputcsv($file, [
                'No',
                'Tanggal',
                'Waktu Input',
                'Mata Pelajaran',
                'Guru Pengajar',
                'Status Kehadiran Guru',
                'Materi Pembelajaran',
                'Jumlah Siswa Hadir',
            ]);

            foreach ($jurnals as $idx => $j) {
                fputcsv($file, [
                    $idx + 1,
                    date('d-m-Y', strtotime($j->tanggal)),
                    date('H:i', strtotime($j->waktu_input)),
                    $j->nama_mapel,
                    $j->nama_guru,
                    $j->status_kehadiran_guru,
                    $j->materi ?? '-',
                    $j->jumlah_hadir,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

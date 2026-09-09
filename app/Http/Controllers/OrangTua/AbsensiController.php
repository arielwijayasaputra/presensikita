<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\DispenSiswa;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
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
        $idSiswa = session('auth_siswa_id');
        $siswa = Siswa::with('kelas')->find($idSiswa);

        if (! $siswa) {
            session()->forget(['auth_siswa_id', 'auth_nisn', 'auth_nama_siswa', 'auth_role']);

            return redirect()->route('login')->withErrors(['nisn' => 'Siswa tidak ditemukan.']);
        }

        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $data = $this->getPortalData($siswa, $tanggal);

        return view('orangtua.dashboard', $data);
    }

    public function realtime(Request $request)
    {
        $idSiswa = session('auth_siswa_id');
        $siswa = Siswa::with('kelas')->find($idSiswa);

        if (! $siswa) {
            return response()->json(['status' => 'error', 'message' => 'Siswa tidak ditemukan.'], 404);
        }

        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $data = $this->getPortalData($siswa, $tanggal);

        return response()->json([
            'status' => 'success',
            'data' => [
                'tanggal' => $data['tanggal'],
                'hari' => $data['hariIndo'],
                'waktuServer' => $data['waktuServer'],
                'statHarian' => $data['statHarian'],
                'pctHadirBulan' => $data['pctHadirBulan'],
                'presensiPerJam' => $data['presensiPerJam'],
                'rekapPerMapel' => $data['rekapPerMapel'],
            ],
        ]);
    }

    private function getPortalData(Siswa $siswa, string $tanggal): array
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');

        // Status dispen anak hari ini: keluar sekolah ATAU masih di dalam sekolah (jenis D).
        // Tidak mengikuti filter tanggal presensi agar wali selalu melihat kondisi terkini.
        $dispenHariIni = DispenSiswa::with('guruPiket')
            ->where('id_siswa', $siswa->id_siswa)
            ->where('jenis_absen', 'D')
            ->where(function ($q) {
                $q->whereDate('tanggal_dispen', now()->toDateString())
                    ->orWhere(function ($q2) {
                        $q2->whereNotNull('waktu_keluar')->whereNull('waktu_masuk');
                    });
            })
            ->latest('id_dispen_siswa')
            ->first();

        $riwayatDispen = DispenSiswa::with('guruPiket')
            ->where('id_siswa', $siswa->id_siswa)
            ->orderByDesc('tanggal_dispen')
            ->orderByDesc('id_dispen_siswa')
            ->get();

        // Nama hari bahasa Indonesia
        $dayMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        $dayNum = date('N', strtotime($tanggal));
        $hariIndo = $dayMap[$dayNum] ?? 'Senin';

        // 1. Sinkronkan presensi per jam untuk kelas anak pada tanggal ini secara otomatis.
        // Jam yang sudah selesai akan di-save permanen dengan absensi terakhir pada jam tersebut.
        if ($siswa->id_kelas && $tanggal <= now()->toDateString()) {
            $this->absensiService->syncPresensiPerJam((int) $siswa->id_kelas, $tanggal);
        }

        // 2. Ambil Jadwal Mengajar Kelas Siswa pada Hari Tersebut
        $jadwalList = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->join('guru', 'jadwal_mengajar.id_guru', '=', 'guru.id_guru')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->whereNull('mapel.deleted_at')
            ->whereNull('guru.deleted_at')
            ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
            ->where('jadwal_mengajar.hari', $hariIndo)
            ->select(
                'jadwal_mengajar.id_jadwal',
                'jam_pelajaran.jam_ke',
                'jam_pelajaran.jam_mulai',
                'jam_pelajaran.jam_selesai',
                'mapel.kode_mapel',
                'mapel.nama_mapel',
                'guru.nama_guru'
            )
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        $isPastDate = ($tanggal < now()->toDateString());
        $isToday = ($tanggal === now()->toDateString());
        $nowTime = now()->format('H:i:s');

        // 3. Ambil Jurnal & Presensi Siswa per Jam Pelajaran pada Tanggal Tersebut
        $presensiPerJam = [];
        $statHarian = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Dispen' => 0, 'Alpa' => 0];

        // Running status absensi terakhir pada hari ini (default: semua siswa Hadir)
        $runningTidakHadir = [];

        foreach ($jadwalList as $j) {
            $isSelesai = $isPastDate || ($isToday && $nowTime >= $j->jam_selesai);
            $isSedangBerlangsung = $isToday && ($nowTime >= $j->jam_mulai && $nowTime < $j->jam_selesai);
            $isUpcoming = $isToday && ($nowTime < $j->jam_mulai);

            $jurnal = JurnalKelas::where('id_jadwal', $j->id_jadwal)
                ->whereDate('tanggal', $tanggal)
                ->first();

            $status = 'Hadir';
            $statusLabel = 'Hadir';
            $materi = '-';
            $keterangan = '-';
            $badgeClass = 'badge-success';

            if ($jurnal) {
                // Jam ini memiliki data jurnal tersimpan
                $materi = $jurnal->materi ?? 'Pembelajaran Harian';

                // Update $runningTidakHadir dari jurnal ini
                $thRows = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->get();
                $runningTidakHadir = [];
                foreach ($thRows as $row) {
                    $runningTidakHadir[$row->id_siswa] = [
                        'status' => $row->status,
                        'keterangan' => $row->keterangan ?? '',
                    ];
                }

                $th = $runningTidakHadir[$siswa->id_siswa] ?? null;
                if ($th) {
                    $ketLower = strtolower($th['keterangan'] ?? '');
                    if ($th['status'] === 'S') {
                        $status = 'Sakit';
                        $statusLabel = 'Sakit';
                        $badgeClass = 'badge-warning';
                    } elseif ($th['status'] === 'D' || str_starts_with($ketLower, 'd:') || str_contains($ketLower, 'dispensasi')) {
                        $status = 'Dispen';
                        $statusLabel = 'Dispensasi';
                        $badgeClass = 'badge-dispen';
                    } elseif ($th['status'] === 'I') {
                        $status = 'Izin';
                        $statusLabel = 'Izin';
                        $badgeClass = 'badge-info';
                    } else {
                        $status = 'Alpa';
                        $statusLabel = 'Alpa';
                        $badgeClass = 'badge-danger';
                    }
                    $keterangan = $th['keterangan'] ?: '-';
                } else {
                    $status = 'Hadir';
                    $statusLabel = 'Hadir';
                    $badgeClass = 'badge-success';
                }
            } else {
                // Jam ini belum memiliki jurnal sendiri (jam sedang berjalan atau jam berikutnya).
                // Status mengikuti status absensi yang disimpan terakhir ("sedangkan yang absen lainnya tetap sesuai dengan absen yang disimpan terakhir")
                $th = $runningTidakHadir[$siswa->id_siswa] ?? null;
                if ($th) {
                    $ketLower = strtolower($th['keterangan'] ?? '');
                    if ($th['status'] === 'S') {
                        $status = 'Sakit';
                        $statusLabel = 'Sakit';
                        $badgeClass = 'badge-warning';
                    } elseif ($th['status'] === 'D' || str_starts_with($ketLower, 'd:') || str_contains($ketLower, 'dispensasi')) {
                        $status = 'Dispen';
                        $statusLabel = 'Dispensasi';
                        $badgeClass = 'badge-dispen';
                    } elseif ($th['status'] === 'I') {
                        $status = 'Izin';
                        $statusLabel = 'Izin';
                        $badgeClass = 'badge-info';
                    } else {
                        $status = 'Alpa';
                        $statusLabel = 'Alpa';
                        $badgeClass = 'badge-danger';
                    }
                    $keterangan = $th['keterangan'] ?: '-';
                } else {
                    $status = 'Hadir';
                    $statusLabel = 'Hadir';
                    $badgeClass = 'badge-success';
                }
            }

            $sessionState = 'finished';
            $sessionLabel = 'Selesai';
            if ($isSedangBerlangsung) {
                $sessionState = 'ongoing';
                $sessionLabel = 'Sedang Berlangsung';
            } elseif ($isUpcoming) {
                $sessionState = 'upcoming';
                $sessionLabel = 'Akan Datang';
            }

            if (isset($statHarian[$status])) {
                $statHarian[$status]++;
            } else {
                $statHarian['Hadir']++;
            }

            $presensiPerJam[] = [
                'jam_ke' => $j->jam_ke,
                'jam_mulai' => date('H:i', strtotime($j->jam_mulai)),
                'jam_selesai' => date('H:i', strtotime($j->jam_selesai)),
                'kode_mapel' => $j->kode_mapel ?? '-',
                'nama_mapel' => $j->nama_mapel,
                'nama_guru' => $j->nama_guru,
                'status' => $status,
                'status_label' => $statusLabel,
                'badge_class' => $badgeClass,
                'materi' => $materi,
                'keterangan' => $keterangan,
                'session_state' => $sessionState,
                'session_label' => $sessionLabel,
                'is_ongoing' => $isSedangBerlangsung,
                'is_finished' => $isSelesai,
            ];
        }

        // 4. Rekap Kehadiran Bulanan Siswa & Per Mapel
        $bulanFilter = date('m', strtotime($tanggal));
        $tahunFilter = date('Y', strtotime($tanggal));

        $jurnalsBulan = DB::table('jurnal_kelas')
            ->join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->whereNull('jurnal_kelas.deleted_at')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('mapel.deleted_at')
            ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
            ->whereMonth('jurnal_kelas.tanggal', $bulanFilter)
            ->whereYear('jurnal_kelas.tanggal', $tahunFilter)
            ->select(
                'jurnal_kelas.id_jurnal',
                'mapel.id_mapel',
                'mapel.kode_mapel',
                'mapel.nama_mapel'
            )
            ->get();

        $totalJamBulan = $jurnalsBulan->count();
        $jurnalIdsBulan = $jurnalsBulan->pluck('id_jurnal')->toArray();

        $tidakHadirBulan = JurnalSiswaTidakHadir::whereIn('id_jurnal', $jurnalIdsBulan)
            ->where('id_siswa', $siswa->id_siswa)
            ->get();

        $sakitBulan = $tidakHadirBulan->where('status', 'S')->count();
        $dispenBulan = $tidakHadirBulan->filter(function ($row) {
            $k = strtolower($row->keterangan ?? '');

            return $row->status === 'D' || str_starts_with($k, 'd:') || str_contains($k, 'dispensasi');
        })->count();
        $izinBulan = $tidakHadirBulan->filter(function ($row) {
            $k = strtolower($row->keterangan ?? '');

            return $row->status === 'I' && ! str_starts_with($k, 'd:') && ! str_contains($k, 'dispensasi');
        })->count();
        $alpaBulan = $tidakHadirBulan->where('status', 'A')->count();
        $hadirBulan = max(0, $totalJamBulan - ($sakitBulan + $izinBulan + $dispenBulan + $alpaBulan));

        $pctHadirBulan = $totalJamBulan > 0 ? (int) round(($hadirBulan / $totalJamBulan) * 100) : 0;

        // Breakdown Per Mapel Bulanan
        $rekapPerMapel = [];
        $groupedMapel = $jurnalsBulan->groupBy('id_mapel');

        foreach ($groupedMapel as $idMapel => $items) {
            $mapelItem = $items->first();
            $mapelJurnalIds = $items->pluck('id_jurnal')->toArray();
            $totMapel = count($mapelJurnalIds);

            $thMapel = $tidakHadirBulan->whereIn('id_jurnal', $mapelJurnalIds);
            $sMapel = $thMapel->where('status', 'S')->count();
            $iMapel = $thMapel->where('status', 'I')->count();
            $aMapel = $thMapel->where('status', 'A')->count();
            $hMapel = max(0, $totMapel - ($sMapel + $iMapel + $aMapel));

            $pctMapel = $totMapel > 0 ? (int) round(($hMapel / $totMapel) * 100) : 0;

            $rekapPerMapel[] = [
                'nama_mapel' => $mapelItem->nama_mapel,
                'kode_mapel' => $mapelItem->kode_mapel ?? '-',
                'total_jam' => $totMapel,
                'hadir' => $hMapel,
                'sakit' => $sMapel,
                'izin' => $iMapel,
                'alpa' => $aMapel,
                'persentase' => $pctMapel,
            ];
        }

        return [
            'siswa' => $siswa,
            'tahunAjaran' => $tahunAjaran,
            'namaSekolah' => $namaSekolah,
            'tanggal' => $tanggal,
            'hariIndo' => $hariIndo,
            'presensiPerJam' => $presensiPerJam,
            'statHarian' => $statHarian,
            'totalJamBulan' => $totalJamBulan,
            'hadirBulan' => $hadirBulan,
            'sakitBulan' => $sakitBulan,
            'izinBulan' => $izinBulan,
            'alpaBulan' => $alpaBulan,
            'pctHadirBulan' => $pctHadirBulan,
            'rekapPerMapel' => $rekapPerMapel,
            'dispenHariIni' => $dispenHariIni,
            'riwayatDispen' => $riwayatDispen,
            'waktuServer' => now()->format('H:i:s'),
        ];
    }
}

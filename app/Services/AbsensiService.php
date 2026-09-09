<?php

namespace App\Services;

use App\Models\Hari;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiService
{
    /**
     * Agregasi absensi dari jurnal_kelas + jurnal_siswa_tidak_hadir (sumber tunggal).
     */
    public function buildAbsensiRekap(
        ?int $kelasId = null,
        ?int $bulan = null,
        ?int $tahun = null,
        bool $withCharts = true,
        bool $withSiswa = true
    ): array {
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
            ->whereNull('jurnal_kelas.deleted_at')
            ->whereNull('jadwal_mengajar.deleted_at')
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
                    if (! isset($perSiswaStatus[$row->id_siswa])) {
                        $perSiswaStatus[$row->id_siswa] = ['S' => 0, 'I' => 0, 'A' => 0];
                    }
                    if (isset($perSiswaStatus[$row->id_siswa][$st])) {
                        $perSiswaStatus[$row->id_siswa][$st]++;
                    }
                }
            }
        }

        $sakit = $statusCounts['S'];
        $izin = $statusCounts['I'];
        $alpa = $statusCounts['A'];
        $totalEvents = $hadir + $sakit + $izin + $alpa;

        $pctHadir = $totalEvents > 0 ? (int) round(($hadir / $totalEvents) * 100) : 0;
        $pctSakit = $totalEvents > 0 ? (int) round(($sakit / $totalEvents) * 100) : 0;
        $pctIzin = $totalEvents > 0 ? (int) round(($izin / $totalEvents) * 100) : 0;
        $pctAlpa = $totalEvents > 0 ? max(0, 100 - $pctHadir - $pctSakit - $pctIzin) : 0;

        $siswaRekap = [];
        if ($withSiswa) {
            foreach ($siswaList as $s) {
                $st = $perSiswaStatus[$s->id_siswa] ?? ['S' => 0, 'I' => 0, 'A' => 0];
                $sSakit = $st['S'];
                $sIzin = $st['I'];
                $sAlpa = $st['A'];
                $sHadir = max(0, $jurnalCount - $sSakit - $sIzin - $sAlpa);
                $pct = $jurnalCount > 0 ? (int) round(($sHadir / $jurnalCount) * 100) : 0;
                $ket = $jurnalCount === 0
                    ? '-'
                    : ($pct >= 90 ? 'Sangat Baik' : ($pct >= 80 ? 'Baik' : ($pct >= 70 ? 'Cukup' : 'Kurang')));

                $siswaRekap[] = [
                    'id_siswa' => $s->id_siswa,
                    'nama_siswa' => $s->nama_siswa,
                    'nisn' => $s->nisn,
                    'jenis_kelamin' => $s->jenis_kelamin ?? 'L',
                    'no_hp_ortu' => $s->no_hp_ortu ?? '',
                    'hadir' => $sHadir,
                    'sakit' => $sSakit,
                    'izin' => $sIzin,
                    'alpa' => $sAlpa,
                    'persentase' => $pct,
                    'keterangan' => $ket,
                ];
            }
        }

        return [
            'total_siswa' => $totalSiswa,
            'jurnal_count' => $jurnalCount,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'pct_hadir' => $pctHadir,
            'pct_sakit' => $pctSakit,
            'pct_izin' => $pctIzin,
            'pct_alpa' => $pctAlpa,
            'pct_label' => $pctHadir >= 90 ? 'Sangat Baik' : ($pctHadir >= 80 ? 'Baik' : ($pctHadir >= 70 ? 'Cukup' : ($totalEvents > 0 ? 'Kurang' : '-'))),
            'siswa' => $siswaRekap,
            'tren' => $withCharts ? $this->buildTrenKehadiran(10, $kelasId, $bulan, $tahun) : null,
            'rekap_hari' => $withCharts ? $this->buildRekapPerHari($kelasId, $bulan, $tahun) : null,
        ];
    }

    public function buildTrenKehadiran(int $days = 7, ?int $kelasId = null, ?int $bulan = null, ?int $tahun = null): array
    {
        $labels = [];
        $hadir = [];
        $sakit = [];
        $izin = [];
        $alpa = [];
        $pct = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = ($bulan && $tahun)
                ? Carbon::createFromDate($tahun, $bulan, 1)->addDays($i)->toDateString()
                : now()->subDays($i)->toDateString();
            $labels[] = Carbon::parse($date)->format('d/m');

            $q = DB::table('jurnal_kelas')
                ->join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                ->whereNull('jurnal_kelas.deleted_at')
                ->whereNull('jadwal_mengajar.deleted_at')
                ->whereDate('jurnal_kelas.tanggal', $date);

            if ($kelasId) {
                $q->where('jadwal_mengajar.id_kelas', $kelasId);
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

    public function buildRekapPerHari(?int $kelasId = null, ?int $bulan = null, ?int $tahun = null): array
    {
        $days = Hari::where('is_aktif', 1)->where('urutan', '<=', 6)->orderBy('urutan')->get();
        $map = [];
        foreach ($days as $d) {
            $map[$d->urutan + 1] = $d->singkatan;
        }
        $result = [];
        foreach ($map as $dow => $label) {
            $result[$label] = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];
        }

        $q = DB::table('jurnal_kelas')
            ->join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->whereNull('jurnal_kelas.deleted_at')
            ->whereNull('jadwal_mengajar.deleted_at')
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
                'hadir' => array_fill(0, 6, 0),
                'sakit' => array_fill(0, 6, 0),
                'izin' => array_fill(0, 6, 0),
                'alpa' => array_fill(0, 6, 0),
            ];
        }

        $idsByDow = [];
        foreach ($rows as $row) {
            $dow = (int) $row->dow;
            if (! isset($map[$dow])) {
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
            $result[$label]['sakit'] += (int) ($counts['S'] ?? 0);
            $result[$label]['izin'] += (int) ($counts['I'] ?? 0);
            $result[$label]['alpa'] += (int) ($counts['A'] ?? 0);
        }

        return [
            'labels' => array_values($map),
            'hadir' => array_map(fn ($l) => $result[$l]['hadir'], array_values($map)),
            'sakit' => array_map(fn ($l) => $result[$l]['sakit'], array_values($map)),
            'izin' => array_map(fn ($l) => $result[$l]['izin'], array_values($map)),
            'alpa' => array_map(fn ($l) => $result[$l]['alpa'], array_values($map)),
        ];
    }

    public function buildPersentasePerKelas(int $limit = 6): array
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
                'id_kelas' => $kelas->id_kelas,
                'nama_kelas' => $kelas->nama_kelas,
                'persentase' => $rekap['pct_hadir'],
                'total_siswa' => $rekap['total_siswa'],
            ];
        }

        usort($items, fn ($a, $b) => $b['persentase'] <=> $a['persentase']);

        return array_slice($items, 0, $limit);
    }

    public function buildAbsensiRekapRange(
        ?int $kelasId = null,
        ?string $tglMulai = null,
        ?string $tglSelesai = null,
        bool $withSiswa = true
    ): array {
        $siswaQuery = Siswa::where('is_aktif', 1);
        if ($kelasId) {
            $siswaQuery->where('id_kelas', $kelasId);
        }

        $siswaList = $withSiswa ? $siswaQuery->orderBy('nama_siswa')->get() : collect();
        $totalSiswa = $withSiswa ? $siswaList->count() : (clone $siswaQuery)->count();

        $jurnalQ = DB::table('jurnal_kelas')
            ->join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->whereNull('jurnal_kelas.deleted_at')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->select('jurnal_kelas.id_jurnal', 'jurnal_kelas.tanggal', 'jurnal_kelas.jumlah_hadir', 'jadwal_mengajar.id_kelas');

        if ($kelasId) {
            $jurnalQ->where('jadwal_mengajar.id_kelas', $kelasId);
        }
        if ($tglMulai) {
            $jurnalQ->whereDate('jurnal_kelas.tanggal', '>=', $tglMulai);
        }
        if ($tglSelesai) {
            $jurnalQ->whereDate('jurnal_kelas.tanggal', '<=', $tglSelesai);
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
                    if (! isset($perSiswaStatus[$row->id_siswa])) {
                        $perSiswaStatus[$row->id_siswa] = ['S' => 0, 'I' => 0, 'A' => 0];
                    }
                    if (isset($perSiswaStatus[$row->id_siswa][$st])) {
                        $perSiswaStatus[$row->id_siswa][$st]++;
                    }
                }
            }
        }

        $sakit = $statusCounts['S'];
        $izin = $statusCounts['I'];
        $alpa = $statusCounts['A'];
        $totalEvents = $hadir + $sakit + $izin + $alpa;

        $pctHadir = $totalEvents > 0 ? (int) round(($hadir / $totalEvents) * 100) : 0;
        $pctSakit = $totalEvents > 0 ? (int) round(($sakit / $totalEvents) * 100) : 0;
        $pctIzin = $totalEvents > 0 ? (int) round(($izin / $totalEvents) * 100) : 0;
        $pctAlpa = $totalEvents > 0 ? max(0, 100 - $pctHadir - $pctSakit - $pctIzin) : 0;

        $siswaRekap = [];
        if ($withSiswa) {
            foreach ($siswaList as $s) {
                $st = $perSiswaStatus[$s->id_siswa] ?? ['S' => 0, 'I' => 0, 'A' => 0];
                $sSakit = $st['S'];
                $sIzin = $st['I'];
                $sAlpa = $st['A'];
                $sHadir = max(0, $jurnalCount - $sSakit - $sIzin - $sAlpa);
                $pct = $jurnalCount > 0 ? (int) round(($sHadir / $jurnalCount) * 100) : 0;
                $ket = $jurnalCount === 0
                    ? '-'
                    : ($pct >= 90 ? 'Sangat Baik' : ($pct >= 80 ? 'Baik' : ($pct >= 70 ? 'Cukup' : 'Kurang')));

                $siswaRekap[] = [
                    'id_siswa' => $s->id_siswa,
                    'nama_siswa' => $s->nama_siswa,
                    'nisn' => $s->nisn,
                    'jenis_kelamin' => $s->jenis_kelamin ?? 'L',
                    'no_hp_ortu' => $s->no_hp_ortu ?? '',
                    'hadir' => $sHadir,
                    'sakit' => $sSakit,
                    'izin' => $sIzin,
                    'alpa' => $sAlpa,
                    'persentase' => $pct,
                    'keterangan' => $ket,
                ];
            }
        }

        return [
            'total_siswa' => $totalSiswa,
            'jurnal_count' => $jurnalCount,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'pct_hadir' => $pctHadir,
            'pct_sakit' => $pctSakit,
            'pct_izin' => $pctIzin,
            'pct_alpa' => $pctAlpa,
            'pct_label' => $pctHadir >= 90 ? 'Sangat Baik' : ($pctHadir >= 80 ? 'Baik' : ($pctHadir >= 70 ? 'Cukup' : ($totalEvents > 0 ? 'Kurang' : '-'))),
            'siswa' => $siswaRekap,
        ];
    }

    /**
     * Sinkronisasi otomatis presensi per jam untuk kelas dan tanggal tertentu.
     * Aturan:
     * - Default kehadiran adalah Hadir (H).
     * - Setiap jam yang sudah berjalan/selesai, jika belum memiliki jurnal sendiri,
     *   akan otomatis menyimpan presensi sesuai dengan status absensi terakhir pada saat itu.
     * - Jurnal jam sebelumnya tidak akan tertimpa saat ada perubahan absensi di jam berikutnya.
     */
    public function syncPresensiPerJam(int $kelasId, string $tanggal): void
    {
        if ($tanggal > now()->toDateString()) {
            return;
        }

        $dayNum = date('N', strtotime($tanggal));
        $dayMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        $hariIndo = $dayMap[$dayNum] ?? 'Senin';

        $jadwalList = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('jam_pelajaran.deleted_at')
            ->where('jadwal_mengajar.id_kelas', $kelasId)
            ->where('jadwal_mengajar.hari', $hariIndo)
            ->select(
                'jadwal_mengajar.id_jadwal',
                'jadwal_mengajar.id_guru',
                'jam_pelajaran.jam_ke',
                'jam_pelajaran.jam_mulai',
                'jam_pelajaran.jam_selesai'
            )
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        if ($jadwalList->isEmpty()) {
            return;
        }

        $allSiswa = Siswa::where('id_kelas', $kelasId)->where('is_aktif', 1)->get();
        $totalSiswa = $allSiswa->count();
        if ($totalSiswa === 0) {
            return;
        }

        $isPastDate = ($tanggal < now()->toDateString());
        $isToday = ($tanggal === now()->toDateString());
        $nowTime = now()->format('H:i:s');

        // Status berjalan ketidakhadiran siswa (default: kosong -> semua Hadir)
        $currentTidakHadir = [];

        foreach ($jadwalList as $j) {
            $isSelesai = $isPastDate || ($isToday && $nowTime >= $j->jam_selesai);

            $jurnal = JurnalKelas::withTrashed()
                ->where('id_jadwal', $j->id_jadwal)
                ->whereDate('tanggal', $tanggal)
                ->first();

            if ($jurnal) {
                if ($jurnal->trashed()) {
                    $jurnal->restore();
                }

                $thRows = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->get();
                $currentTidakHadir = [];
                foreach ($thRows as $row) {
                    $currentTidakHadir[$row->id_siswa] = [
                        'id_siswa' => $row->id_siswa,
                        'status' => $row->status,
                        'keterangan' => $row->keterangan ?? '',
                    ];
                }
            } else {
                // Jam ini belum memiliki jurnal sendiri.
                // Jika jam pelajaran sudah selesai, simpan otomatis (ngesv) absen terakhir di jam itu
                if ($isSelesai) {
                    $jumlahHadir = max(0, $totalSiswa - count($currentTidakHadir));
                    $waktuInput = $isToday ? now() : Carbon::parse($tanggal . ' ' . $j->jam_selesai);

                    $newJurnal = JurnalKelas::create([
                        'id_jadwal' => $j->id_jadwal,
                        'id_guru' => $j->id_guru,
                        'tanggal' => $tanggal,
                        'status_kehadiran_guru' => 'Hadir',
                        'materi' => 'Pembelajaran Harian',
                        'jumlah_hadir' => $jumlahHadir,
                        'waktu_input' => $waktuInput,
                    ]);

                    foreach ($currentTidakHadir as $th) {
                        JurnalSiswaTidakHadir::create([
                            'id_jurnal' => $newJurnal->id_jurnal,
                            'id_siswa' => $th['id_siswa'],
                            'status' => $th['status'],
                            'keterangan' => $th['keterangan'],
                        ]);
                    }
                }
            }
        }
    }
}

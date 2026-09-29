<?php

namespace App\Http\Controllers\Struktural;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\IzinGuru;
use App\Models\JurnalKelas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataGuruController extends Controller
{
    /**
     * Daftar seluruh guru aktif beserta rekap kehadiran mengajar,
     * jumlah pengajuan izin, dan keterangan lainya.
     */
    public function rekap(int $bulan, int $tahun): array
    {
        $allGuru = Guru::where('is_admin', 0)->where('is_aktif', 1)->with('mapel')->orderBy('nama_guru')->get();

        $sesiPerGuru = $this->hitungTotalSesiPerGuru($bulan, $tahun);
        $izinPerGuru = $this->hitungIzinPerGuru($bulan, $tahun);
        $jurnal = JurnalKelas::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get(['id_guru', 'status_kehadiran_guru']);

        $rekap = [];

        foreach ($allGuru as $g) {
            $idGuru = (int) $g->id_guru;

            $guruJurnal = $jurnal->where('id_guru', $idGuru);
            $hadir = $guruJurnal->where('status_kehadiran_guru', 'Hadir')->count();
            $tidakHadir = $guruJurnal->where('status_kehadiran_guru', 'Tidak Hadir')->count();

            $totalSesi = $sesiPerGuru[$idGuru] ?? 0;
            $belumIsi = max(0, $totalSesi - ($hadir + $tidakHadir));

            $izin = $izinPerGuru[$idGuru] ?? [
                'total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0,
            ];

            $rekap[] = [
                'id_guru' => $idGuru,
                'nip' => $g->nip ?? '-',
                'nama_guru' => $g->nama_guru,
                'username' => $g->username,
                'no_hp' => $g->no_hp ?? '-',
                'nama_mapel' => $g->mapel?->nama_mapel ?? '-',
                'total_sesi' => $totalSesi,
                'hadir' => $hadir,
                'tidak_hadir' => $tidakHadir,
                'belum_isi' => $belumIsi,
                'izin_total' => $izin['total'],
                'izin_disetujui' => $izin['disetujui'],
                'izin_ditolak' => $izin['ditolak'],
                'izin_menunggu' => $izin['menunggu'],
                'persentase' => $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 1) : 0,
            ];
        }

        return $rekap;
    }

    /**
     * Rincian data kehadiran seorang guru (dipakai saat baris tabel diklik).
     */
    public function detail(Request $request, Guru $guru)
    {
        abort_unless(session('auth_role') === 'waka_sdm', 403);

        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));
        $bulan = $bulan >= 1 && $bulan <= 12 ? $bulan : (int) date('n');

        $idGuru = (int) $guru->id_guru;
        $tglMulai = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
        $tglSelesai = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

        $sesiPerGuru = $this->hitungTotalSesiPerGuru($bulan, $tahun);
        $totalSesi = $sesiPerGuru[$idGuru] ?? 0;

        $jurnal = JurnalKelas::where('id_guru', $idGuru)
            ->whereBetween('tanggal', [$tglMulai, $tglSelesai])
            ->get();

        $hadir = $jurnal->where('status_kehadiran_guru', 'Hadir')->count();
        $tidakHadir = $jurnal->where('status_kehadiran_guru', 'Tidak Hadir')->count();
        $belumIsi = max(0, $totalSesi - ($hadir + $tidakHadir));

        $izinList = IzinGuru::where('id_guru', $idGuru)
            ->whereBetween('tanggal_izin', [$tglMulai, $tglSelesai])
            ->orderByDesc('tanggal_izin')
            ->get();

        $izinDisetujui = $izinList->filter(fn ($i) => $i->status_kepsek === 'disetujui' && $i->status_waka === 'disetujui')->count();
        $izinDitolak = $izinList->filter(fn ($i) => $i->status_kepsek === 'ditolak' || $i->status_waka === 'ditolak')->count();
        $izinMenunggu = $izinList->count() - $izinDisetujui - $izinDitolak;

        $riwayat = $this->riwayatKehadiran($idGuru, $tglMulai, $tglSelesai);

        return response()->json([
            'guru' => [
                'id_guru' => $idGuru,
                'nama_guru' => $guru->nama_guru,
                'nip' => $guru->nip ?? '-',
                'username' => $guru->username,
                'no_hp' => $guru->no_hp ?: '-',
                'nama_mapel' => $guru->mapel?->nama_mapel ?? '-',
            ],
            'periode' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
            ],
            'statistik' => [
                'total_sesi' => $totalSesi,
                'hadir' => $hadir,
                'tidak_hadir' => $tidakHadir,
                'belum_isi' => $belumIsi,
                'izin_total' => $izinList->count(),
                'izin_disetujui' => $izinDisetujui,
                'izin_ditolak' => $izinDitolak,
                'izin_menunggu' => $izinMenunggu,
                'persentase' => $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 1) : 0,
            ],
            'izin' => $izinList->map(fn ($i) => [
                'tanggal' => $i->tanggal_izin->format('d-m-Y'),
                'alasan' => $i->alasan ?: '-',
                'status_kepsek' => $i->status_kepsek,
                'status_waka' => $i->status_waka,
                'status' => $this->statusIzinGuru($i),
                'catatan' => $i->catatan_waka ?: ($i->catatan_kepsek ?: '-'),
            ])->values(),
            'riwayat' => $riwayat,
            'per_bulan' => $this->rekapPerBulan($idGuru, $tahun),
        ]);
    }

    /**
     * Riwayat kehadiran harian guru (jurnal yang sudah diisi) pada suatu periode.
     */
    private function riwayatKehadiran(int $idGuru, string $tglMulai, string $tglSelesai): array
    {
        return JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->leftJoin('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->leftJoin('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->leftJoin('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->whereNull('jurnal_kelas.deleted_at')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->where('jurnal_kelas.id_guru', $idGuru)
            ->whereBetween('jurnal_kelas.tanggal', [$tglMulai, $tglSelesai])
            ->orderByDesc('jurnal_kelas.tanggal')
            ->orderByDesc('jurnal_kelas.waktu_input')
            ->select(
                'jurnal_kelas.tanggal',
                'jurnal_kelas.status_kehadiran_guru',
                'jurnal_kelas.materi',
                'jurnal_kelas.jumlah_hadir',
                'jurnal_kelas.waktu_input',
                'mapel.nama_mapel',
                'kelas.nama_kelas',
                'jam_pelajaran.jam_ke',
                'jam_pelajaran.jam_mulai',
                'jam_pelajaran.jam_selesai'
            )
            ->get()
            ->map(fn ($r) => [
                'tanggal' => Carbon::parse($r->tanggal)->format('d-m-Y'),
                'hari' => Hari::getNamaHariFromDayOfWeek((int) Carbon::parse($r->tanggal)->dayOfWeekIso),
                'jam' => 'Ke-'.((int) $r->jam_ke >= 100 ? (int) $r->jam_ke - 100 : (int) $r->jam_ke)
                    .' ('.substr((string) $r->jam_mulai, 0, 5).'-'.substr((string) $r->jam_selesai, 0, 5).')',
                'mapel' => $r->nama_mapel ?? '-',
                'kelas' => $r->nama_kelas ?? '-',
                'status' => $r->status_kehadiran_guru,
                'materi' => $r->materi ?: '-',
                'siswa_hadir' => (int) $r->jumlah_hadir,
                'waktu_input' => $r->waktu_input ? Carbon::parse($r->waktu_input)->format('d-m-Y H:i') : '-',
            ])
            ->values()
            ->all();
    }

    /**
     * Rekap kehadiran guru per bulan sepanjang satu tahun.
     */
    private function rekapPerBulan(int $idGuru, int $tahun): array
    {
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'urutan')->toArray();

        $jadwal = DB::table('jadwal_mengajar')
            ->whereNull('deleted_at')
            ->where('id_guru', $idGuru)
            ->get(['hari']);

        $jurnal = JurnalKelas::where('id_guru', $idGuru)
            ->whereYear('tanggal', $tahun)
            ->get(['tanggal', 'status_kehadiran_guru']);

        $hasil = [];

        for ($m = 1; $m <= 12; $m++) {
            $dayCounts = [];
            $jumlahHari = Carbon::createFromDate($tahun, $m, 1)->daysInMonth;
            for ($d = 1; $d <= $jumlahHari; $d++) {
                $date = Carbon::createFromDate($tahun, $m, $d);
                $hariNama = $hariMap[$date->dayOfWeekIso] ?? null;
                if ($hariNama) {
                    $dayCounts[$hariNama] = ($dayCounts[$hariNama] ?? 0) + 1;
                }
            }

            $totalSesi = 0;
            foreach ($jadwal as $j) {
                $totalSesi += ($dayCounts[$j->hari] ?? 0);
            }

            $jurnalBulan = $jurnal->filter(fn ($x) => (int) Carbon::parse($x->tanggal)->month === $m);
            $hadir = $jurnalBulan->where('status_kehadiran_guru', 'Hadir')->count();
            $tidakHadir = $jurnalBulan->where('status_kehadiran_guru', 'Tidak Hadir')->count();

            $hasil[] = [
                'bulan' => $m,
                'nama_bulan' => $this->namaBulan($m),
                'total_sesi' => $totalSesi,
                'hadir' => $hadir,
                'tidak_hadir' => $tidakHadir,
                'belum_isi' => max(0, $totalSesi - ($hadir + $tidakHadir)),
                'persentase' => $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 1) : 0,
            ];
        }

        return $hasil;
    }

    /**
     * Total sesi mengajar terjadwal per guru pada bulan/tahun tertentu.
     */
    private function hitungTotalSesiPerGuru(int $bulan, int $tahun): array
    {
        $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'urutan')->toArray();

        $dayCounts = [];
        $jumlahHari = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        for ($d = 1; $d <= $jumlahHari; $d++) {
            $date = Carbon::createFromDate($tahun, $bulan, $d);
            $hariNama = $hariMap[$date->dayOfWeekIso] ?? null;
            if ($hariNama) {
                $dayCounts[$hariNama] = ($dayCounts[$hariNama] ?? 0) + 1;
            }
        }

        $jadwal = DB::table('jadwal_mengajar')
            ->whereNull('deleted_at')
            ->select('id_guru', 'hari')
            ->get();

        $hasil = [];
        foreach ($jadwal as $j) {
            $idGuru = (int) $j->id_guru;
            $hasil[$idGuru] = ($hasil[$idGuru] ?? 0) + ($dayCounts[$j->hari] ?? 0);
        }

        return $hasil;
    }

    /**
     * Jumlah pengajuan izin guru per guru pada bulan/tahun tertentu.
     */
    private function hitungIzinPerGuru(int $bulan, int $tahun): array
    {
        $izin = IzinGuru::whereYear('tanggal_izin', $tahun)
            ->whereMonth('tanggal_izin', $bulan)
            ->get(['id_guru', 'status_kepsek', 'status_waka']);

        $hasil = [];

        foreach ($izin as $i) {
            $idGuru = (int) $i->id_guru;

            $hasil[$idGuru] ??= [
                'total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0,
            ];

            $hasil[$idGuru]['total']++;
            $hasil[$idGuru][$this->statusIzinGuru($i)]++;
        }

        return $hasil;
    }

    /**
     * Gabungan status izin dari persetujuan kepsek & waka.
     */
    private function statusIzinGuru(IzinGuru $izin): string
    {
        if ($izin->status_kepsek === 'ditolak' || $izin->status_waka === 'ditolak') {
            return 'ditolak';
        }

        if ($izin->status_kepsek === 'disetujui' && $izin->status_waka === 'disetujui') {
            return 'disetujui';
        }

        return 'menunggu';
    }

    private function namaBulan(int $bulan): string
    {
        $daftar = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $daftar[$bulan] ?? 'Bulan_'.$bulan;
    }
}

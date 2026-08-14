<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Pengaturan;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $idSiswa = session('auth_siswa_id');
        $siswa = Siswa::with('kelas')->find($idSiswa);

        if (!$siswa) {
            session()->forget(['auth_siswa_id', 'auth_nisn', 'auth_nama_siswa', 'auth_role']);
            return redirect()->route('login')->withErrors(['nisn' => 'Siswa tidak ditemukan.']);
        }

        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');

        // Tanggal filter harian (default: hari ini)
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Nama hari bahasa Indonesia
        $dayMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        $dayNum = date('N', strtotime($tanggal));
        $hariIndo = $dayMap[$dayNum] ?? 'Senin';

        // 1. Ambil Jadwal Mengajar Kelas Siswa pada Hari Tersebut
        $jadwalList = DB::table('jadwal_mengajar')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->join('guru', 'jadwal_mengajar.id_guru', '=', 'guru.id_guru')
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

        // 2. Ambil Jurnal & Presensi Siswa per Jam Pelajaran pada Tanggal Tersebut
        $presensiPerJam = [];
        $statHarian = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0, 'Belum' => 0];

        foreach ($jadwalList as $j) {
            $jurnal = JurnalKelas::where('id_jadwal', $j->id_jadwal)
                ->whereDate('tanggal', $tanggal)
                ->first();

            $status = 'Belum';
            $statusLabel = 'Belum Ada Presensi';
            $materi = '-';
            $keterangan = '-';
            $badgeClass = 'bg-gray-100 text-gray-600';

            if ($jurnal) {
                $materi = $jurnal->materi ?? 'Pembelajaran Harian';

                $tidakHadir = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)
                    ->where('id_siswa', $siswa->id_siswa)
                    ->first();

                if ($tidakHadir) {
                    if ($tidakHadir->status === 'S') {
                        $status = 'Sakit';
                        $statusLabel = 'Sakit';
                        $badgeClass = 'badge-warning';
                    } elseif ($tidakHadir->status === 'I') {
                        $status = 'Izin';
                        $statusLabel = 'Izin';
                        $badgeClass = 'badge-info';
                    } else {
                        $status = 'Alpa';
                        $statusLabel = 'Alpa';
                        $badgeClass = 'badge-danger';
                    }
                    $keterangan = $tidakHadir->keterangan ?? '-';
                } else {
                    $status = 'Hadir';
                    $statusLabel = 'Hadir';
                    $badgeClass = 'badge-success';
                }
            }

            $statHarian[$status]++;

            $presensiPerJam[] = [
                'jam_ke'      => $j->jam_ke,
                'jam_mulai'   => date('H:i', strtotime($j->jam_mulai)),
                'jam_selesai' => date('H:i', strtotime($j->jam_selesai)),
                'kode_mapel'  => $j->kode_mapel ?? '-',
                'nama_mapel'  => $j->nama_mapel,
                'nama_guru'   => $j->nama_guru,
                'status'      => $status,
                'status_label'=> $statusLabel,
                'badge_class' => $badgeClass,
                'materi'      => $materi,
                'keterangan'  => $keterangan,
            ];
        }

        // 3. Rekap Kehadiran Bulanan Siswa & Per Mapel
        $bulanFilter = date('m', strtotime($tanggal));
        $tahunFilter = date('Y', strtotime($tanggal));

        $jurnalsBulan = DB::table('jurnal_kelas')
            ->join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
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
        $izinBulan  = $tidakHadirBulan->where('status', 'I')->count();
        $alpaBulan  = $tidakHadirBulan->where('status', 'A')->count();
        $hadirBulan = max(0, $totalJamBulan - ($sakitBulan + $izinBulan + $alpaBulan));

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
                'total_jam'  => $totMapel,
                'hadir'      => $hMapel,
                'sakit'      => $sMapel,
                'izin'       => $iMapel,
                'alpa'       => $aMapel,
                'persentase' => $pctMapel
            ];
        }

        return view('orangtua.dashboard', compact(
            'siswa',
            'tahunAjaran',
            'namaSekolah',
            'tanggal',
            'hariIndo',
            'presensiPerJam',
            'statHarian',
            'totalJamBulan',
            'hadirBulan',
            'sakitBulan',
            'izinBulan',
            'alpaBulan',
            'pctHadirBulan',
            'rekapPerMapel'
        ));
    }
}

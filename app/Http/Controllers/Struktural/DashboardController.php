<?php

namespace App\Http\Controllers\Struktural;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Pengaturan;
use App\Models\TahunAjaran;
use App\Models\JamPelajaran;
use App\Models\GuruPiket;
use App\Models\IzinGuru;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $guru = Guru::find(session('auth_guru_id'));
        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $sidebar = 'partials.sidebar_struktural';
        $profilUpdateUrl = route('struktural.profil.update');
        $isGuruPiket = session('auth_role') === 'guru_piket';
        $guruAktif = Guru::where('is_admin', 0)->where('is_aktif', 1)->orderBy('nama_guru')->get();
        $hariMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $hariIni = $hariMap[now()->dayOfWeekIso];
        $jadwalHariIni = DB::table('jadwal_mengajar')
            ->join('guru', 'jadwal_mengajar.id_guru', '=', 'guru.id_guru')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
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
            'jamAktif'
            , 'guruAktif', 'izinGuruTerbaru', 'isGuruPiket'
        ));
    }
}

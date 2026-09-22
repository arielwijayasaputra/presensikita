<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Services\AbsensiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Menyediakan data rekap laporan absensi siswa.
 */
class LaporanController extends Controller
{
    /**
     * Menyuntikkan layanan absensi untuk membangun rekap.
     */
    public function __construct(protected AbsensiService $absensiService) {}

    /**
     * Mengambil data rekap absensi siswa berdasarkan kelas, bulan, dan tahun.
     *
     * @return JsonResponse
     */
    public function getData(Request $request)
    {
        $kelasId = (int) $request->get('kelas_id');
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));
        $filter = $request->get('data', 'semua');

        if (! $kelasId) {
            return response()->json(['status' => 'error', 'message' => 'Kelas wajib dipilih.'], 422);
        }

        $kelas = Kelas::find($kelasId);
        if (! $kelas) {
            return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan.'], 404);
        }

        $rekap = $this->absensiService->buildAbsensiRekap($kelasId, $bulan, $tahun);

        if ($filter !== 'semua') {
            $rekap['siswa'] = array_values(array_filter($rekap['siswa'], function ($row) use ($filter) {
                return ($row[$filter] ?? 0) > 0;
            }));
        }

        $rekap['nama_kelas'] = $kelas->nama_kelas;

        return response()->json(['status' => 'success', 'data' => $rekap]);
    }

    /**
     * Mengekspor laporan rekap absensi siswa dalam format PDF standar.
     */
    public function exportPdf(Request $request)
    {
        $kelasId = (int) $request->get('kelas_id');
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));
        $filter = $request->get('data', 'semua');

        $kelases = Kelas::orderBy('nama_kelas')->get();
        if (! $kelasId && $kelases->isNotEmpty()) {
            $kelasId = $kelases->first()->id_kelas;
        }

        $kelas = Kelas::find($kelasId) ?? $kelases->first();
        abort_unless($kelas, 404, 'Kelas tidak ditemukan.');

        $monthsMap = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $namaBulan = $monthsMap[$bulan] ?? 'Bulan ' . $bulan;

        $rekap = $this->absensiService->buildAbsensiRekap($kelas->id_kelas, $bulan, $tahun);

        $siswaList = $rekap['siswa'] ?? [];
        if ($filter !== 'semua') {
            $siswaList = array_values(array_filter($siswaList, function ($row) use ($filter) {
                return ($row[$filter] ?? 0) > 0;
            }));
        }

        $waliKelas = $kelas->id_wali_kelas ? \App\Models\Guru::find($kelas->id_wali_kelas) : null;
        $tahunAjaran = \App\Models\TahunAjaran::where('is_aktif', 1)->first() ?? \App\Models\TahunAjaran::first();
        $kepalaSekolahNama = \App\Models\Pengaturan::get('kepsek', 'Drs. H. Mulyono, M.Pd.');

        $data = [
            'namaKelas' => $kelas->nama_kelas,
            'namaBulan' => $namaBulan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tahunAjaran' => $tahunAjaran,
            'waliKelasNama' => $waliKelas?->nama_guru ?? '-',
            'waliKelasNip' => $waliKelas?->nip ?? '-',
            'kepalaSekolahNama' => $kepalaSekolahNama ?: 'Drs. H. Mulyono, M.Pd.',
            'kepalaSekolahNip' => '19680512 199412 1 002',
            'totalSiswa' => $rekap['total_siswa'] ?? count($siswaList),
            'totalHadir' => $rekap['hadir'] ?? 0,
            'totalSakit' => $rekap['sakit'] ?? 0,
            'totalIzin' => $rekap['izin'] ?? 0,
            'totalDispen' => $rekap['dispen'] ?? 0,
            'totalAlpa' => $rekap['alpa'] ?? 0,
            'siswaList' => $siswaList,
        ];

        $namaFileKelas = str_replace(' ', '_', $kelas->nama_kelas);
        $filename = "Laporan_Presensi_{$namaFileKelas}_{$namaBulan}_{$tahun}.pdf";

        $pdf = Pdf::loadView('exports.pdf.laporan_absensi', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        return $pdf->download($filename);
    }
}


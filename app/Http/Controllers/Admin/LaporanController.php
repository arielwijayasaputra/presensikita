<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Services\AbsensiService;
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
}

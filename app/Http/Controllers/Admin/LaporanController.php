<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AbsensiService;
use App\Models\Kelas;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(protected AbsensiService $absensiService) {}

    public function getData(Request $request)
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

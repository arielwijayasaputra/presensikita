<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportSiswaRequest;
use App\Http\Requests\Admin\StoreSiswaRequest;
use App\Http\Requests\Admin\UpdateSiswaRequest;
use App\Models\Siswa;
use App\Services\Import\SiswaImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mengelola data siswa: impor dari file CSV/Excel, CRUD, dan penghapusan massal.
 */
class SiswaController extends Controller
{
    public function __construct(private SiswaImportService $siswaImportService) {}

    /**
     * Mengimpor data siswa dari file CSV atau Excel, lalu membuat atau memperbarui data siswa berdasarkan NISN.
     *
     * @return JsonResponse
     */
    public function importCsv(ImportSiswaRequest $request)
    {
        return $this->siswaImportService->import($request->file('file_csv'));
    }

    /**
     * Menghapus seluruh data siswa dari sistem.
     *
     * @return JsonResponse
     */
    public function hapusSemua(Request $request)
    {
        $total = Siswa::count();
        if ($total === 0) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data siswa untuk dihapus.'], 422);
        }

        Siswa::query()->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Semua data siswa berhasil dihapus ($total siswa).",
            'deleted' => $total,
        ]);
    }

    /**
     * Menyimpan data siswa baru berdasarkan input dari request.
     *
     * @return JsonResponse
     */
    public function store(StoreSiswaRequest $request)
    {
        $siswa = Siswa::create([
            'nama_siswa' => $request->nama_siswa,
            'nisn' => $request->nisn,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'is_aktif' => 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Siswa berhasil ditambahkan!',
            'data' => $siswa,
        ]);
    }

    /**
     * Memperbarui data siswa yang sudah ada berdasarkan ID.
     *
     * @return JsonResponse
     */
    public function update(UpdateSiswaRequest $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->update([
            'nama_siswa' => $request->nama_siswa,
            'nisn' => $request->nisn,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data siswa berhasil diperbarui!',
            'data' => $siswa,
        ]);
    }

    /**
     * Menghapus data siswa berdasarkan ID.
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data siswa berhasil dihapus!',
        ]);
    }
}

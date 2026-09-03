<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mengelola data mata pelajaran.
 */
class MapelController extends Controller
{
    /**
     * Menyimpan data mata pelajaran baru.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'nullable|string|max:20|unique:mapel,kode_mapel',
            'kelompok' => 'nullable|string|in:A,B,C',
        ]);

        $mapel = Mapel::create([
            'kode_mapel' => $request->kode_mapel ?: null,
            'nama_mapel' => $request->nama_mapel,
            'kelompok' => $request->kelompok ?: null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Mata pelajaran berhasil ditambahkan!',
            'data' => $mapel,
        ]);
    }

    /**
     * Memperbarui data mata pelajaran yang sudah ada.
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'nullable|string|max:20|unique:mapel,kode_mapel,'.$mapel->id_mapel.',id_mapel',
            'kelompok' => 'nullable|string|in:A,B,C',
        ]);

        $mapel->update([
            'kode_mapel' => $request->kode_mapel ?: null,
            'nama_mapel' => $request->nama_mapel,
            'kelompok' => $request->kelompok ?: null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Mata pelajaran berhasil diperbarui!',
            'data' => $mapel,
        ]);
    }

    /**
     * Menghapus data mata pelajaran bila tidak lagi dipakai pada jadwal mengajar.
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        $jadwalCount = $mapel->jadwal()->count();

        if ($jadwalCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Mapel tidak dapat dihapus karena masih dipakai di {$jadwalCount} jadwal mengajar.",
            ], 422);
        }

        $mapel->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Mata pelajaran berhasil dihapus!',
        ]);
    }
}

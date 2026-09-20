<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMapelRequest;
use App\Http\Requests\Admin\UpdateMapelRequest;
use App\Models\Mapel;
use Illuminate\Http\JsonResponse;

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
    public function store(StoreMapelRequest $request)
    {
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
    public function update(UpdateMapelRequest $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

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

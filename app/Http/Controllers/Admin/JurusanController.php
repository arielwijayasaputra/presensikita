<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJurusanRequest;
use App\Http\Requests\Admin\UpdateJurusanRequest;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;

/**
 * Mengelola data jurusan di sekolah.
 */
class JurusanController extends Controller
{
    /**
     * Menyimpan data jurusan baru.
     *
     * @return JsonResponse
     */
    public function store(StoreJurusanRequest $request)
    {
        $jurusan = Jurusan::create([
            'kode_jurusan' => strtoupper(trim($request->kode_jurusan)),
            'nama_jurusan' => trim($request->nama_jurusan),
            'deskripsi' => $request->deskripsi ? trim($request->deskripsi) : null,
            'is_aktif' => 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jurusan berhasil ditambahkan!',
            'data' => $jurusan,
        ]);
    }

    /**
     * Memperbarui data jurusan yang sudah ada.
     *
     * @return JsonResponse
     */
    public function update(UpdateJurusanRequest $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $oldKode = $jurusan->kode_jurusan;
        $newKode = strtoupper(trim($request->kode_jurusan));

        $jurusan->update([
            'kode_jurusan' => $newKode,
            'nama_jurusan' => trim($request->nama_jurusan),
            'deskripsi' => $request->deskripsi ? trim($request->deskripsi) : null,
        ]);

        if ($oldKode !== $newKode) {
            Kelas::where('jurusan', $oldKode)->update(['jurusan' => $newKode]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data jurusan berhasil diperbarui!',
            'data' => $jurusan,
        ]);
    }

    /**
     * Mengaktifkan atau menonaktifkan status jurusan.
     *
     * @return JsonResponse
     */
    public function toggleAktif($id)
    {
        $jurusan = Jurusan::findOrFail($id);

        // Jika jurusan saat ini aktif dan akan dinonaktifkan, periksa apakah masih ada siswa aktif (bukan alumni)
        if ($jurusan->is_aktif) {
            $siswaAktifCount = $jurusan->countSiswaAktif();

            if ($siswaAktifCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Jurusan {$jurusan->nama_jurusan} ({$jurusan->kode_jurusan}) tidak dapat dinonaktifkan karena masih terdapat {$siswaAktifCount} siswa aktif (bukan alumni) yang terdaftar pada jurusan ini.",
                    'siswa_aktif_count' => $siswaAktifCount,
                ], 422);
            }
        }

        $jurusan->is_aktif = $jurusan->is_aktif ? 0 : 1;
        $jurusan->save();

        return response()->json([
            'status' => 'success',
            'message' => $jurusan->is_aktif ? 'Jurusan berhasil diaktifkan!' : 'Jurusan berhasil dinonaktifkan!',
            'is_aktif' => $jurusan->is_aktif,
        ]);
    }

    /**
     * Menghapus data jurusan bila tidak lagi dipakai siswa atau kelas.
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);

        // Cek siswa aktif (bukan alumni)
        $siswaAktifCount = $jurusan->countSiswaAktif();
        if ($siswaAktifCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Jurusan {$jurusan->nama_jurusan} ({$jurusan->kode_jurusan}) tidak dapat dihapus karena masih terdapat {$siswaAktifCount} siswa aktif (bukan alumni) yang terdaftar pada jurusan ini.",
                'siswa_aktif_count' => $siswaAktifCount,
            ], 422);
        }

        $kelasCount = Kelas::where('jurusan', $jurusan->kode_jurusan)->count();
        if ($kelasCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Jurusan tidak dapat dihapus karena masih digunakan oleh {$kelasCount} data kelas terdaftar. Silakan hapus atau pindahkan kelas terlebih dahulu.",
                'kelas_count' => $kelasCount,
            ], 422);
        }

        $jurusan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jurusan berhasil dihapus!',
        ]);
    }
}

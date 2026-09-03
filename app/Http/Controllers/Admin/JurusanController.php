<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusan,kode_jurusan',
            'nama_jurusan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'kode_jurusan.required' => 'Kode jurusan wajib diisi.',
            'kode_jurusan.unique' => 'Kode jurusan sudah digunakan.',
            'nama_jurusan.required' => 'Nama jurusan wajib diisi.',
        ]);

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
    public function update(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusan,kode_jurusan,'.$jurusan->id_jurusan.',id_jurusan',
            'nama_jurusan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'kode_jurusan.required' => 'Kode jurusan wajib diisi.',
            'kode_jurusan.unique' => 'Kode jurusan sudah digunakan.',
            'nama_jurusan.required' => 'Nama jurusan wajib diisi.',
        ]);

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

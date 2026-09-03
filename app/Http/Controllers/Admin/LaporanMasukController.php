<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Role;
use App\Models\StatusLaporan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Mengelola laporan masuk dari pengguna/masyarakat.
 */
class LaporanMasukController extends Controller
{
    /**
     * Menampilkan form publik untuk laporan di luar login.
     *
     * @return View
     */
    public function showPublicForm()
    {
        $roles = Role::orderBy('id_role')->pluck('nama_role')->toArray();
        $roles[] = 'Masyarakat / Umum';

        return view('laporan_public', compact('roles'));
    }

    /**
     * Menyimpan laporan baru dari form luar login / pengguna.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function storePublic(Request $request)
    {
        $request->validate([
            'role_pelapor' => 'required|string|max:50',
            'nama_pelapor' => 'required|string|max:100',
            'judul' => 'required|string|max:150',
            'isi_laporan' => 'required|string',
        ], [
            'role_pelapor.required' => 'Pilih peran/role Anda terlebih dahulu.',
            'nama_pelapor.required' => 'Nama pelapor wajib diisi.',
            'judul.required' => 'Judul laporan wajib diisi.',
            'isi_laporan.required' => 'Isi laporan wajib diisi.',
        ]);

        $laporan = Laporan::create([
            'role_pelapor' => $request->role_pelapor,
            'nama_pelapor' => $request->nama_pelapor,
            'judul' => $request->judul,
            'isi_laporan' => $request->isi_laporan,
            'status' => 'menunggu',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Laporan Anda berhasil dikirim dan sedang menunggu peninjauan Admin.',
                'data' => $laporan,
            ]);
        }

        return redirect()->back()->with('success_laporan', 'Laporan Anda berhasil dikirim dan sedang menunggu peninjauan Admin.');
    }

    /**
     * Memperbarui status laporan melalui transisi yang diperbolehkan.
     *
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $laporan = Laporan::find($id);

        if (! $laporan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data laporan tidak ditemukan.',
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:'.implode(',', StatusLaporan::getSlugs()),
        ]);

        $laporan->status = $request->status;
        if ($request->has('catatan_admin')) {
            $laporan->catatan_admin = $request->catatan_admin;
        }
        $laporan->save();

        $statusLabels = StatusLaporan::pluck('deskripsi_status', 'slug_status')->toArray();

        return response()->json([
            'status' => 'success',
            'message' => $statusLabels[$request->status] ?? 'Status laporan berhasil diperbarui.',
            'data' => $laporan,
        ]);
    }

    /**
     * Menghapus data laporan.
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $laporan = Laporan::find($id);

        if (! $laporan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Laporan tidak ditemukan.',
            ], 404);
        }

        $laporan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data laporan berhasil dihapus.',
        ]);
    }
}

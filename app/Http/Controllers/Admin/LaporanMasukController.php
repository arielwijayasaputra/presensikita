<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Role;
use Illuminate\Http\Request;

class LaporanMasukController extends Controller
{
    /**
     * Tampilkan form publik untuk laporan di luar login.
     */
    public function showPublicForm()
    {
        $roles = [
            'Guru',
            'Wali Kelas',
            'Guru Piket',
            'Waka Kesiswaan',
            'Kepala Sekolah',
            'Satpam',
            'Orang Tua',
            'Siswa',
            'Masyarakat / Umum',
        ];

        return view('laporan_public', compact('roles'));
    }

    /**
     * Simpan laporan baru dari form luar login / pengguna.
     */
    public function storePublic(Request $request)
    {
        $request->validate([
            'role_pelapor' => 'required|string|max:50',
            'nama_pelapor' => 'required|string|max:100',
            'judul'        => 'required|string|max:150',
            'isi_laporan'  => 'required|string',
        ], [
            'role_pelapor.required' => 'Pilih peran/role Anda terlebih dahulu.',
            'nama_pelapor.required' => 'Nama pelapor wajib diisi.',
            'judul.required'        => 'Judul laporan wajib diisi.',
            'isi_laporan.required'  => 'Isi laporan wajib diisi.',
        ]);

        $laporan = Laporan::create([
            'role_pelapor' => $request->role_pelapor,
            'nama_pelapor' => $request->nama_pelapor,
            'judul'        => $request->judul,
            'isi_laporan'  => $request->isi_laporan,
            'status'       => 'menunggu',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Laporan Anda berhasil dikirim dan sedang menunggu peninjauan Admin.',
                'data'    => $laporan,
            ]);
        }

        return redirect()->back()->with('success_laporan', 'Laporan Anda berhasil dikirim dan sedang menunggu peninjauan Admin.');
    }

    /**
     * Update status laporan dari admin:
     * Transisi:
     * - menunggu  -> diterima / ditolak
     * - diterima  -> diproses / dibatalkan
     * - diproses  -> selesai  / dibatalkan
     */
    public function updateStatus(Request $request, $id)
    {
        $laporan = Laporan::find($id);

        if (!$laporan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data laporan tidak ditemukan.',
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:menunggu,diterima,ditolak,diproses,selesai,dibatalkan',
        ]);

        $laporan->status = $request->status;
        if ($request->has('catatan_admin')) {
            $laporan->catatan_admin = $request->catatan_admin;
        }
        $laporan->save();

        $statusLabels = [
            'diterima'  => 'Laporan telah DITERIMA. Anda dapat melanjutkannya ke proses.',
            'ditolak'   => 'Laporan telah DITOLAK.',
            'diproses'  => 'Laporan sedang DIPROSES.',
            'selesai'   => 'Laporan telah SELESAI ditangani.',
            'dibatalkan' => 'Laporan telah DIBATALKAN.',
            'menunggu'  => 'Status laporan dikembalikan ke MENUNGGU.',
        ];

        return response()->json([
            'status'  => 'success',
            'message' => $statusLabels[$request->status] ?? 'Status laporan berhasil diperbarui.',
            'data'    => $laporan,
        ]);
    }

    /**
     * Hapus data laporan.
     */
    public function destroy($id)
    {
        $laporan = Laporan::find($id);

        if (!$laporan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Laporan tidak ditemukan.',
            ], 404);
        }

        $laporan->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data laporan berhasil dihapus.',
        ]);
    }
}

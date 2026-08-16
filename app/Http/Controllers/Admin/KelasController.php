<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'    => 'required',
            'tingkat_kelas' => 'required',
            'jurusan'       => 'required',
        ]);

        $tahun = TahunAjaran::first()?->id_tahun_ajaran ?? 1;

        $kelas = Kelas::create([
            'nama_kelas'      => $request->nama_kelas,
            'tingkat_kelas'   => $request->tingkat_kelas,
            'jurusan'         => $request->jurusan,
            'id_tahun_ajaran' => $tahun,
            'id_wali_kelas'   => $request->id_wali_kelas ?: null,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kelas berhasil ditambahkan!',
            'data'    => $kelas,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required',
        ]);

        $kelas = Kelas::findOrFail($id);

        $kelas->update([
            'nama_kelas'    => $request->nama_kelas,
            'tingkat_kelas' => $request->tingkat_kelas ?? $kelas->tingkat_kelas,
            'jurusan'       => $request->jurusan ?? $kelas->jurusan,
            'id_wali_kelas' => $request->has('id_wali_kelas') && $request->id_wali_kelas !== '' && $request->id_wali_kelas !== null
                ? $request->id_wali_kelas
                : null,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kelas berhasil diperbarui!',
            'data'    => $kelas,
        ]);
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kelas berhasil dihapus!',
        ]);
    }
}

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
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kelas berhasil ditambahkan!',
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa'    => 'required',
            'nisn'          => 'required',
            'id_kelas'      => 'required',
            'jenis_kelamin' => 'required',
        ]);

        $siswa = Siswa::create([
            'nama_siswa'    => $request->nama_siswa,
            'nisn'          => $request->nisn,
            'id_kelas'      => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'is_aktif'      => 1,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Siswa berhasil ditambahkan!',
            'data'    => $siswa,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_siswa'    => 'required',
            'nisn'          => 'required',
            'id_kelas'      => 'required',
            'jenis_kelamin' => 'required',
        ]);

        $siswa = Siswa::findOrFail($id);
        $siswa->update([
            'nama_siswa'    => $request->nama_siswa,
            'nisn'          => $request->nisn,
            'id_kelas'      => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data siswa berhasil diperbarui!',
            'data'    => $siswa,
        ]);
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data siswa berhasil dihapus!',
        ]);
    }
}

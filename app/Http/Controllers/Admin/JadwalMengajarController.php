<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalMengajar;
use Illuminate\Http\Request;

class JadwalMengajarController extends Controller
{
    public function update(Request $request, $id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);

        $data = $request->validate([
            'id_guru' => ['required', 'integer', 'exists:guru,id_guru'],
            'id_mapel' => ['required', 'integer', 'exists:mapel,id_mapel'],
            'id_kelas' => ['required', 'integer', 'exists:kelas,id_kelas'],
            'id_jam' => ['required', 'integer', 'exists:jam_pelajaran,id_jam'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
        ]);

        $jadwal->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal mengajar berhasil diperbarui.',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_guru' => ['required', 'integer', 'exists:guru,id_guru'],
            'id_mapel' => ['required', 'integer', 'exists:mapel,id_mapel'],
            'id_kelas' => ['required', 'integer', 'exists:kelas,id_kelas'],
            'id_jam' => ['required', 'integer', 'exists:jam_pelajaran,id_jam'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
            'id_tahun_ajaran' => ['required', 'integer', 'exists:tahun_ajaran,id_tahun_ajaran'],
        ]);

        JadwalMengajar::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal mengajar berhasil ditambahkan.',
        ]);
    }

    public function destroy($id)
    {
        JadwalMengajar::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal mengajar berhasil dihapus.',
        ]);
    }
}

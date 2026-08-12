<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Pengaturan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'semester'     => 'required|string',
        ], [
            'nama_sekolah.required' => 'Nama sekolah tidak boleh kosong.',
            'tahun_ajaran.required' => 'Tahun ajaran tidak boleh kosong.',
            'semester.required'     => 'Semester tidak boleh kosong.',
        ]);

        Pengaturan::set('nama_sekolah', trim($request->nama_sekolah));
        if ($request->filled('sistem_absensi')) {
            Pengaturan::set('sistem_absensi', trim($request->sistem_absensi));
        }

        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        if ($tahun) {
            $tahun->update([
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester'     => trim($request->semester),
            ]);
        } else {
            TahunAjaran::create([
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester'     => trim($request->semester),
                'is_aktif'     => 1,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengaturan sistem berhasil diperbarui!',
            'data'    => [
                'nama_sekolah'   => trim($request->nama_sekolah),
                'tahun_ajaran'   => trim($request->tahun_ajaran),
                'semester'       => trim($request->semester),
                'sistem_absensi' => trim($request->sistem_absensi ?? Pengaturan::get('sistem_absensi')),
            ],
        ]);
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required',
            'no_hp'     => 'nullable',
        ]);

        $guru = Guru::where('Peran', 'Wali Kelas')->first() ?? Guru::first();
        if ($guru) {
            $guru->update([
                'nama_guru' => $request->nama_guru,
                'no_hp'     => $request->no_hp,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui!',
        ]);
    }
}

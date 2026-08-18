<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required|string',
            'username'  => 'required|string|unique:guru,username',
            'password'  => 'required|string|min:4',
        ], [
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan guru lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 4 karakter.',
        ]);

        $guru = Guru::create([
            'nip'           => $request->nip ?: null,
            'nama_guru'     => $request->nama_guru,
            'Peran'         => $request->peran ?? 'Guru',
            'no_hp'         => $request->no_hp ?: null,
            'username'      => strtolower(trim($request->username)),
            'password_hash' => Hash::make($request->password),
            'is_admin'      => $request->is_admin ? 1 : 0,
            'is_aktif'      => 1,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Guru berhasil ditambahkan!',
            'data'    => $guru,
        ]);
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nama_guru' => 'required|string',
            'username'  => 'required|string|unique:guru,username,' . $guru->id_guru . ',id_guru',
        ], [
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan guru lain.',
        ]);

        $data = [
            'nip'       => $request->nip ?: null,
            'nama_guru' => $request->nama_guru,
            'Peran'     => $request->peran ?? 'Guru',
            'no_hp'     => $request->no_hp ?: null,
            'username'  => strtolower(trim($request->username)),
            'is_admin'  => $request->is_admin ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $guru->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data guru berhasil diperbarui!',
            'data'    => $guru,
        ]);
    }

    public function toggleAktif($id)
    {
        $guru = Guru::findOrFail($id);

        if ($guru->id_guru == session('auth_guru_id')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri!',
            ], 422);
        }

        $guru->is_aktif = $guru->is_aktif ? 0 : 1;
        $guru->save();

        return response()->json([
            'status'  => 'success',
            'message' => $guru->is_aktif ? 'Guru berhasil diaktifkan!' : 'Guru berhasil dinonaktifkan!',
            'is_aktif' => $guru->is_aktif,
        ]);
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);

        if ($guru->id_guru == session('auth_guru_id')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri!',
            ], 422);
        }

        $guru->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data guru berhasil dihapus!',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Guru;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('auth_guru_id')) {
            $guru = Guru::find(session('auth_guru_id'));
            if ($guru && $guru->is_admin) {
                return redirect()->route('admin.index');
            }
            return redirect()->route('guru.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $guru = Guru::where('username', $request->username)
            ->where('is_aktif', 1)
            ->first();

        if (!$guru || !Hash::check($request->password, $guru->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username'));
        }

        // Simpan data guru ke session
        session([
            'auth_guru_id'   => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin'  => $guru->is_admin,
        ]);

        // Redirect berdasarkan role
        if ($guru->is_admin) {
            return redirect()->route('admin.index');
        }

        return redirect()->route('guru.index');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['auth_guru_id', 'auth_nama_guru', 'auth_is_admin']);
        return redirect()->route('login');
    }
}

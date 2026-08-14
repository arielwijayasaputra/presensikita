<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Guru;
use App\Models\Siswa;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login Guru / Admin
     */
    public function showLogin()
    {
        if (session('auth_guru_id')) {
            $guru = Guru::find(session('auth_guru_id'));
            if ($guru && $guru->is_admin) {
                return redirect()->route('admin.index');
            }
            return redirect()->route('guru.index');
        }

        if (session('auth_siswa_id')) {
            return redirect()->route('orangtua.index');
        }

        return view('auth.login');
    }

    /**
     * Proses login Guru / Admin
     */
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
            'auth_role'      => $guru->is_admin ? 'admin' : 'guru',
        ]);

        // Redirect berdasarkan role
        if ($guru->is_admin) {
            return redirect()->route('admin.index');
        }

        return redirect()->route('guru.index');
    }

    /**
     * Proses login Orang Tua via NISN Siswa (Database Connected)
     */
    public function loginOrangTua(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
        ], [
            'nisn.required' => 'NISN Siswa tidak boleh kosong.',
        ]);

        $rawNisn = trim($request->nisn);

        // 1. Cari exact match terlebih dahulu
        $siswa = Siswa::where('nisn', $rawNisn)->first();

        // 2. Jika tidak ada, coba variasi format NISN (tanpa nol di depan atau dengan 10 digit zero padding)
        if (!$siswa) {
            $unpadded = ltrim($rawNisn, '0');
            $padded = sprintf('%010d', (int)$rawNisn);

            $siswa = Siswa::whereIn('nisn', [$rawNisn, $unpadded, $padded])->first();
        }

        // 3. Jika tetap tidak ditemukan
        if (!$siswa) {
            return back()->withErrors([
                'nisn' => 'NISN (' . htmlspecialchars($rawNisn) . ') tidak ditemukan di database sekolah.',
            ])->withInput();
        }

        // 4. Cek status aktif
        if (isset($siswa->is_aktif) && $siswa->is_aktif == 0) {
            return back()->withErrors([
                'nisn' => 'Status siswa dengan NISN tersebut sedang tidak aktif.',
            ])->withInput();
        }

        // 5. Simpan data orang tua/siswa ke session
        session([
            'auth_siswa_id'   => $siswa->id_siswa,
            'auth_nisn'       => $siswa->nisn,
            'auth_nama_siswa' => $siswa->nama_siswa,
            'auth_role'       => 'orangtua',
        ]);

        return redirect()->route('orangtua.index');
    }

    /**
     * Logout untuk semua role
     */
    public function logout(Request $request)
    {
        $request->session()->forget([
            'auth_guru_id',
            'auth_nama_guru',
            'auth_is_admin',
            'auth_siswa_id',
            'auth_nisn',
            'auth_nama_siswa',
            'auth_role'
        ]);

        return redirect()->route('login');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login Guru / Admin
     */
    public function showLogin()
    {
        if (session('auth_guru_id')) {
            $guru = Guru::find(session('auth_guru_id'));
            $role = session('auth_role', '');

            if ($guru && $guru->is_admin) {
                return redirect()->route('admin.index');
            }

            $strukturalMap = [
                'waka'      => 'waka.index',
                'kepsek'    => 'kepsek.index',
                'satpam'    => 'satpam.index',
                'guru_piket'=> 'gurupiket.index',
                'walikelas' => 'walikelas.index',
            ];

            if (isset($strukturalMap[$role])) {
                return redirect()->route($strukturalMap[$role]);
            }

            return redirect()->route('guru.index');
        }

        if (session('auth_siswa_id')) {
            return redirect()->route('orangtua.index');
        }

        $kelases = Kelas::orderBy('nama_kelas')->get();

        return view('auth.login', compact('kelases'));
    }

    /**
     * Proses login Guru / Admin
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role'     => 'required|in:admin,guru',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
            'role.required'     => 'Pilih peran terlebih dahulu.',
            'role.in'           => 'Peran tidak valid.',
        ]);

        $guru = Guru::where('username', $request->username)->first();

        if (!$guru) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'role'));
        }

        if ($guru->is_aktif == 0) {
            return back()->withErrors([
                'username' => 'Akun anda telah dinonaktifkan.',
            ])->withInput($request->only('username', 'role'));
        }

        if (!Hash::check($request->password, $guru->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'role'));
        }

        $roleForm = $request->role;
        $roleGuru = $guru->is_admin ? 'admin' : 'guru';

        if ($roleForm !== $roleGuru) {
            return back()->withErrors([
                'username' => 'Akun tersebut bukan ' . $roleForm . '.',
            ])->withInput($request->only('username', 'role'));
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
     * Proses login Wali Kelas (Guru yang menjadi wali dari suatu kelas)
     */
    public function loginWaliKelas(Request $request)
    {
        $request->validate([
            'nip'      => 'required|string',
            'password' => 'required|string',
        ], [
            'nip.required'      => 'NIP tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $guru = Guru::where('nip', $request->nip)->first();

        if (!$guru) {
            return back()->withErrors([
                'nip' => 'NIP atau password salah.',
            ])->withInput($request->only('nip', 'role'));
        }

        if ($guru->is_aktif == 0) {
            return back()->withErrors([
                'nip' => 'Akun anda telah dinonaktifkan.',
            ])->withInput($request->only('nip', 'role'));
        }

        if (!Hash::check($request->password, $guru->password_hash)) {
            return back()->withErrors([
                'nip' => 'NIP atau password salah.',
            ])->withInput($request->only('nip', 'role'));
        }

        // Kelas yang guru tersebut menjadi wali kelasnya
        $kelasWali = Kelas::where('id_wali_kelas', $guru->id_guru)->first();

        if (!$kelasWali) {
            return back()->withErrors([
                'nip' => 'Akun tersebut bukan wali kelas.',
            ])->withInput($request->only('nip', 'role'));
        }

        // Simpan data guru + kelas wali ke session
        session([
            'auth_guru_id'    => $guru->id_guru,
            'auth_nama_guru'  => $guru->nama_guru,
            'auth_is_admin'   => 0,
            'auth_role'       => 'walikelas',
            'auth_kelas_id'   => $kelasWali->id_kelas,
            'auth_nama_kelas' => $kelasWali->nama_kelas,
        ]);

        return redirect()->route('walikelas.index', ['kelas_id' => $kelasWali->id_kelas]);
    }

    /**
     * Proses login struktural: Waka Kesiswaan / Kepala Sekolah / Satpam
     * (untuk sementara hanya username & password)
     */
    public function loginPeran(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'peran'    => 'required|in:Waka,Kepsek,Satpam',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
            'peran.required'    => 'Pilih peran terlebih dahulu.',
            'peran.in'          => 'Peran tidak valid.',
        ]);

        $guru = Guru::where('username', $request->username)->first();

        if (!$guru) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'peran', 'role'));
        }

        if ($guru->is_aktif == 0) {
            return back()->withErrors([
                'username' => 'Akun anda telah dinonaktifkan.',
            ])->withInput($request->only('username', 'peran', 'role'));
        }

        if (!Hash::check($request->password, $guru->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'peran', 'role'));
        }

        $peran = $request->peran;

        if (($guru->Peran ?? '') !== $peran) {
            $label = match ($peran) {
                'Waka'   => 'Waka Kesiswaan',
                'Kepsek' => 'Kepala Sekolah',
                default  => 'Satpam',
            };

            return back()->withErrors([
                'username' => 'Akun tersebut bukan ' . $label . '.',
            ])->withInput($request->only('username', 'peran', 'role'));
        }

        // Simpan data guru ke session
        session([
            'auth_guru_id'   => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin'  => 0,
            'auth_role'      => strtolower($peran),
        ]);

        // Redirect berdasarkan peran
        $redirectMap = [
            'Waka'   => 'waka.index',
            'Kepsek' => 'kepsek.index',
            'Satpam' => 'satpam.index',
        ];

        return redirect()->route($redirectMap[$peran] ?? 'guru.index');
    }

    /**
     * Login Kepala Sekolah tanpa credential (bypass)
     */
    public function loginKepsekBypass()
    {
        $guru = Guru::where('Peran', 'Kepsek')
            ->where('is_aktif', 1)
            ->first();

        if (!$guru) {
            return back()->withErrors([
                'username' => 'Akun Kepala Sekolah tidak ditemukan atau tidak aktif.',
            ]);
        }

        session([
            'auth_guru_id'   => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin'  => 0,
            'auth_role'      => 'kepsek',
        ]);

        return redirect()->route('kepsek.index');
    }

    /**
     * Proses login Guru Piket (username + password)
     */
    public function loginGuruPiket(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $guru = Guru::where('username', $request->username)->first();

        if (!$guru) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username'));
        }

        if ($guru->is_aktif == 0) {
            return back()->withErrors([
                'username' => 'Akun anda telah dinonaktifkan.',
            ])->withInput($request->only('username'));
        }

        if (!Hash::check($request->password, $guru->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username'));
        }

        if (($guru->Peran ?? '') !== 'Guru Piket') {
            return back()->withErrors([
                'username' => 'Akun tersebut bukan Guru Piket.',
            ])->withInput($request->only('username'));
        }

        session([
            'auth_guru_id'   => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin'  => 0,
            'auth_role'      => 'guru_piket',
        ]);

        return redirect()->route('gurupiket.index');
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
            'auth_kelas_id',
            'auth_nama_kelas',
            'auth_role'
        ]);

        return redirect()->route('login');
    }
}

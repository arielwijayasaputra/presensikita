<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AkunAdmin;
use App\Models\AkunSatpam;
use App\Models\Guru;
use App\Models\GuruPiket;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login Guru / Admin
     */
    public function showLogin()
    {
        if (session('auth_is_admin')) {
            return redirect()->route('admin.index');
        }

        if (session('auth_role') === 'satpam' && session('auth_satpam_id')) {
            return redirect()->route('satpam.index');
        }

        if (session('auth_guru_id')) {
            $guru = Guru::find(session('auth_guru_id'));
            $role = session('auth_role', '');

            $strukturalMap = Role::where('is_struktural', 1)
                ->pluck('route_name', 'slug_role')
                ->toArray();

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
            'role' => 'required|in:admin,guru',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
            'role.required' => 'Pilih peran terlebih dahulu.',
            'role.in' => 'Peran tidak valid.',
        ]);

        if ($request->role === 'admin') {
            $admin = AkunAdmin::where('username', $request->username)->first();

            if (! $admin) {
                return back()->withErrors([
                    'username' => 'Username atau password salah.',
                ])->withInput($request->only('username', 'role'));
            }

            if (isset($admin->is_aktif) && $admin->is_aktif == 0) {
                return back()->withErrors([
                    'username' => 'Akun admin telah dinonaktifkan.',
                ])->withInput($request->only('username', 'role'));
            }

            $passwordHash = $admin->password ?: $admin->password_hash;
            if (! Hash::check($request->password, $passwordHash)) {
                return back()->withErrors([
                    'username' => 'Username atau password salah.',
                ])->withInput($request->only('username', 'role'));
            }

            // Simpan data admin ke session
            session([
                'auth_admin_id' => $admin->id_admin,
                'auth_guru_id' => $admin->id_admin,
                'auth_nama_admin' => $admin->nama,
                'auth_nama_guru' => $admin->nama,
                'auth_is_admin' => 1,
                'auth_role' => 'admin',
            ]);
            $request->session()->regenerate();

            return redirect()->route('admin.index');
        }

        $guru = Guru::where('username', $request->username)->first();

        if (! $guru) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'role'));
        }

        if ($guru->is_aktif == 0) {
            return back()->withErrors([
                'username' => 'Akun anda telah dinonaktifkan.',
            ])->withInput($request->only('username', 'role'));
        }

        if (! Hash::check($request->password, $guru->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'role'));
        }

        // Simpan data guru ke session
        session([
            'auth_guru_id' => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'guru',
        ]);
        $request->session()->regenerate();

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
        if (! $siswa) {
            $unpadded = ltrim($rawNisn, '0');
            $padded = sprintf('%010d', (int) $rawNisn);

            $siswa = Siswa::whereIn('nisn', [$rawNisn, $unpadded, $padded])->first();
        }

        // 3. Jika tetap tidak ditemukan
        if (! $siswa) {
            return back()->withErrors([
                'nisn' => 'NISN atau kredensial tidak valid.',
            ])->withInput();
        }

        // 4. Cek status aktif
        if (isset($siswa->is_aktif) && $siswa->is_aktif == 0) {
            return back()->withErrors([
                'nisn' => 'NISN atau kredensial tidak valid.',
            ])->withInput();
        }

        // 5. Simpan data orang tua/siswa ke session
        session([
            'auth_siswa_id' => $siswa->id_siswa,
            'auth_nisn' => $siswa->nisn,
            'auth_nama_siswa' => $siswa->nama_siswa,
            'auth_role' => 'orangtua',
        ]);
        $request->session()->regenerate();

        return redirect()->route('orangtua.index');
    }

    /**
     * Proses login Wali Kelas (Guru yang menjadi wali dari suatu kelas)
     */
    public function loginWaliKelas(Request $request)
    {
        $username = $request->username ?? $request->nip;

        $request->validate([
            'username' => 'nullable|string',
            'nip' => 'nullable|string',
            'password' => 'required|string',
        ], [
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        if (empty($username)) {
            return back()->withErrors([
                'username' => 'Username tidak boleh kosong.',
            ])->withInput($request->only('username', 'nip', 'role'));
        }

        $guru = Guru::where('username', $username)
            ->orWhere('nip', $username)
            ->first();

        if (! $guru) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'nip', 'role'));
        }

        if ($guru->is_aktif == 0) {
            return back()->withErrors([
                'username' => 'Akun anda telah dinonaktifkan.',
            ])->withInput($request->only('username', 'nip', 'role'));
        }

        if (! Hash::check($request->password, $guru->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'nip', 'role'));
        }

        // Kelas yang guru tersebut menjadi wali kelasnya
        $kelasWali = Kelas::where('id_wali_kelas', $guru->id_guru)->first();

        if (! $kelasWali) {
            return back()->withErrors([
                'username' => 'Akun tersebut bukan wali kelas.',
            ])->withInput($request->only('username', 'nip', 'role'));
        }

        // Simpan data guru + kelas wali ke session
        session([
            'auth_guru_id' => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin' => $guru->is_admin,
            'auth_role' => 'walikelas',
            'auth_kelas_id' => $kelasWali->id_kelas,
            'auth_nama_kelas' => $kelasWali->nama_kelas,
        ]);
        $request->session()->regenerate();

        return redirect()->route('walikelas.index', ['kelas_id' => $kelasWali->id_kelas]);
    }

    /**
     * Proses login struktural: Waka Kesiswaan / Kepala Sekolah / Satpam
     * (untuk sementara hanya username & password)
     */
    public function loginPeran(Request $request)
    {
        $validPerans = Role::where('is_struktural', 1)
            ->where('slug_role', '!=', 'guru_piket')
            ->where('slug_role', '!=', 'walikelas')
            ->pluck('nama_role')
            ->toArray();

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'peran' => 'required|in:'.implode(',', $validPerans),
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
            'peran.required' => 'Pilih peran terlebih dahulu.',
            'peran.in' => 'Peran tidak valid.',
        ]);

        $peran = $request->peran;
        $roleRecord = Role::where('nama_role', $peran)->first();

        // ── LOGIN SATPAM: pakai tabel akun_satpam sendiri ──
        if ($roleRecord && $roleRecord->slug_role === 'satpam') {
            $satpam = AkunSatpam::where('username', $request->username)->first();

            if (! $satpam) {
                return back()->withErrors([
                    'username' => 'Username atau password salah.',
                ])->withInput($request->only('username', 'peran', 'role'));
            }

            if ($satpam->is_aktif == 0) {
                return back()->withErrors([
                    'username' => 'Akun anda telah dinonaktifkan.',
                ])->withInput($request->only('username', 'peran', 'role'));
            }

            if (! Hash::check($request->password, $satpam->password_hash)) {
                return back()->withErrors([
                    'username' => 'Username atau password salah.',
                ])->withInput($request->only('username', 'peran', 'role'));
            }

            session([
                'auth_satpam_id' => $satpam->id_satpam,
                'auth_guru_id' => null,
                'auth_nama_guru' => $satpam->nama,
                'auth_is_admin' => 0,
                'auth_role' => 'satpam',
            ]);
            $request->session()->regenerate();

            return redirect()->route('satpam.index');
        }

        // ── LOGIN PERAN LAINNYA (Waka, Kepsek, dll): tetap pakai tabel guru ──
        $guru = Guru::where('username', $request->username)->first();

        if (! $guru) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'peran', 'role'));
        }

        if ($guru->is_aktif == 0) {
            return back()->withErrors([
                'username' => 'Akun anda telah dinonaktifkan.',
            ])->withInput($request->only('username', 'peran', 'role'));
        }

        if (! Hash::check($request->password, $guru->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'peran', 'role'));
        }

        if (($guru->Peran ?? '') !== $peran) {
            $label = $roleRecord->nama_role ?? $peran;

            return back()->withErrors([
                'username' => 'Akun tersebut bukan '.$label.'.',
            ])->withInput($request->only('username', 'peran', 'role'));
        }

        // Simpan data guru ke session
        session([
            'auth_guru_id' => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => $roleRecord->slug_role ?? str_replace(' ', '_', strtolower($peran)),
        ]);
        $request->session()->regenerate();

        // Redirect berdasarkan peran
        $routeName = $roleRecord->route_name ?? Role::getRouteFromSlug($roleRecord->slug_role ?? '');

        return redirect()->route($routeName ?? 'guru.index');
    }

    /**
     * Login Kepala Sekolah tanpa credential (bypass)
     */
    public function loginKepsekBypass(Request $request)
    {
        if (! App::environment('local')) {
            return back()->withErrors([
                'username' => 'Bypass hanya tersedia di environment local.',
            ]);
        }

        $guru = Guru::where('Peran', 'Kepsek')
            ->where('is_aktif', 1)
            ->first();

        if (! $guru) {
            return back()->withErrors([
                'username' => 'Akun Kepala Sekolah tidak ditemukan atau tidak aktif.',
            ]);
        }

        session([
            'auth_guru_id' => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin' => 0,
            'auth_role' => 'kepsek',
        ]);
        $request->session()->regenerate();

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

        if (! $guru) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'role'));
        }

        if ($guru->is_aktif == 0) {
            return back()->withErrors([
                'username' => 'Akun anda telah dinonaktifkan.',
            ])->withInput($request->only('username', 'role'));
        }

        if (! Hash::check($request->password, $guru->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'role'));
        }

        $ditugaskanHariIni = GuruPiket::where('id_guru', $guru->id_guru)
            ->whereDate('tanggal', now()->toDateString())
            ->exists();

        if (! $ditugaskanHariIni) {
            return back()->withErrors([
                'username' => 'Akun ini belum ditugaskan sebagai Guru Piket hari ini.',
            ])->withInput($request->only('username', 'role'));
        }

        session([
            'auth_guru_id' => $guru->id_guru,
            'auth_nama_guru' => $guru->nama_guru,
            'auth_is_admin' => $guru->is_admin,
            'auth_role' => 'guru_piket',
        ]);
        $request->session()->regenerate();

        return redirect()->route('gurupiket.index');
    }

    /**
     * Logout untuk semua role
     */
    public function logout(Request $request)
    {
        $request->session()->forget([
            'auth_admin_id',
            'auth_nama_admin',
            'auth_satpam_id',
            'auth_guru_id',
            'auth_nama_guru',
            'auth_is_admin',
            'auth_siswa_id',
            'auth_nisn',
            'auth_nama_siswa',
            'auth_kelas_id',
            'auth_nama_kelas',
            'auth_role',
        ]);

        return redirect()->route('login');
    }
}

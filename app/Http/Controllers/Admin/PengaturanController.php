<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AkunAdmin;
use App\Models\AkunSatpam;
use App\Models\Guru;
use App\Models\Pengaturan;
use App\Models\TahunAjaran;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Mengelola pengaturan sistem sekolah seperti nama sekolah, tahun ajaran, dan profil pengguna.
 */
class PengaturanController extends Controller
{
    /**
     * Memperbarui pengaturan sistem sekolah (nama, tahun ajaran, semester, dan opsi absensi).
     *
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|string',
        ], [
            'nama_sekolah.required' => 'Nama sekolah tidak boleh kosong.',
            'tahun_ajaran.required' => 'Tahun ajaran tidak boleh kosong.',
            'semester.required' => 'Semester tidak boleh kosong.',
        ]);

        Pengaturan::set('nama_sekolah', trim($request->nama_sekolah));
        Pengaturan::set('npsn', trim($request->npsn ?? ''));
        Pengaturan::set('kepsek', trim($request->kepsek ?? ''));
        Pengaturan::set('alamat', trim($request->alamat ?? ''));
        Pengaturan::set('email_sekolah', trim($request->email_sekolah ?? ''));
        Pengaturan::set('telepon_sekolah', trim($request->telepon_sekolah ?? ''));
        if ($request->filled('sistem_absensi')) {
            Pengaturan::set('sistem_absensi', trim($request->sistem_absensi));
        }
        Pengaturan::set('batas_waktu_jurnal', trim($request->batas_waktu_jurnal ?? '23:59'));
        Pengaturan::set('izin_edit_jurnal', $request->has('izin_edit_jurnal') ? '1' : '0');

        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        if ($tahun) {
            $tahun->update([
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester' => trim($request->semester),
            ]);
        } else {
            TahunAjaran::create([
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester' => trim($request->semester),
                'is_aktif' => 1,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaturan sistem berhasil diperbarui!',
            'data' => [
                'nama_sekolah' => trim($request->nama_sekolah),
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester' => trim($request->semester),
                'sistem_absensi' => trim($request->sistem_absensi ?? Pengaturan::get('sistem_absensi')),
                'batas_waktu_jurnal' => trim($request->batas_waktu_jurnal ?? '23:59'),
            ],
        ]);
    }

    /**
     * Memperbarui profil dan kredensial pengguna (admin, satpam, atau guru) berdasarkan sesi aktif.
     *
     * @return JsonResponse
     */
    public function updateProfil(Request $request)
    {
        try {
            if (session('auth_is_admin')) {
                $adminId = session('auth_admin_id') ?? session('auth_guru_id');
                $admin = $adminId ? AkunAdmin::find($adminId) : AkunAdmin::first();

                if (! $admin) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User admin tidak ditemukan.',
                    ], 404);
                }

                $request->validate([
                    'nama_guru' => 'required|string|max:100',
                    'username' => 'required|string|max:50|unique:akun_admin,username,'.$admin->id_admin.',id_admin',
                    'no_hp' => 'nullable|string|max:20',
                    'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                    'new_password' => 'nullable|string|min:4',
                ], [
                    'nama_guru.required' => 'Nama lengkap wajib diisi.',
                    'username.required' => 'Username wajib diisi.',
                    'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
                    'foto.image' => 'File harus berupa gambar.',
                    'foto.max' => 'Ukuran foto maksimal 2MB.',
                    'new_password.min' => 'Password baru minimal 4 karakter.',
                ]);

                $updateData = [
                    'nama' => trim($request->nama_guru),
                    'no_tlp' => trim($request->no_hp ?? ''),
                    'no_hp' => trim($request->no_hp ?? ''),
                ];

                // Ganti Username jika berubah
                $newUsername = trim($request->username);
                if ($newUsername !== $admin->username) {
                    $updateData['username'] = $newUsername;
                    $updateData['update_usn_at'] = now();
                }

                // Ganti Password jika diisi
                if ($request->filled('new_password')) {
                    if (! $request->filled('current_password')) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Password saat ini wajib diisi.',
                        ], 422);
                    }
                    $currentHash = $admin->password ?: $admin->password_hash;
                    if (! Hash::check($request->current_password, $currentHash)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Password saat ini salah.',
                        ], 422);
                    }
                    $newHashed = Hash::make($request->new_password);
                    $updateData['password'] = $newHashed;
                    $updateData['password_hash'] = $newHashed;
                    $updateData['update_pw_at'] = now();
                }

                // Upload Foto Profil jika ada
                if ($request->hasFile('foto')) {
                    $file = $request->file('foto');
                    $filename = 'profile_admin_'.$admin->id_admin.'_'.time().'.'.$file->getClientOriginalExtension();
                    $destinationPath = public_path('uploads/profile');

                    if (! File::exists($destinationPath)) {
                        File::makeDirectory($destinationPath, 0755, true, true);
                    }

                    if (isset($admin->foto_profil) && $admin->foto_profil && File::exists(public_path($admin->foto_profil))) {
                        @File::delete(public_path($admin->foto_profil));
                    }

                    $file->move($destinationPath, $filename);
                    $updateData['foto_profil'] = 'uploads/profile/'.$filename;
                }

                $admin->update($updateData);

                // Update session jika nama admin berubah
                session([
                    'auth_nama_guru' => $admin->nama,
                    'auth_nama_admin' => $admin->nama,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Profil & pengaturan admin berhasil diperbarui!',
                    'foto_profil' => isset($admin->foto_profil) && $admin->foto_profil ? asset($admin->foto_profil) : null,
                    'nama_guru' => $admin->nama,
                    'username' => $admin->username,
                ]);
            }

            if (session('auth_role') === 'satpam' || session('auth_satpam_id')) {
                $satpamId = session('auth_satpam_id');
                $satpam = $satpamId ? AkunSatpam::find($satpamId) : AkunSatpam::first();

                if (! $satpam) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User satpam tidak ditemukan.',
                    ], 404);
                }

                $request->validate([
                    'nama_guru' => 'required|string|max:100',
                    'username' => 'required|string|max:50|unique:akun_satpam,username,'.$satpam->id_satpam.',id_satpam',
                    'no_hp' => 'nullable|string|max:20',
                    'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                    'new_password' => 'nullable|string|min:4',
                ], [
                    'nama_guru.required' => 'Nama lengkap wajib diisi.',
                    'username.required' => 'Username wajib diisi.',
                    'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
                    'foto.image' => 'File harus berupa gambar.',
                    'foto.max' => 'Ukuran foto maksimal 2MB.',
                    'new_password.min' => 'Password baru minimal 4 karakter.',
                ]);

                $updateData = [
                    'nama' => trim($request->nama_guru),
                    'username' => trim($request->username),
                    'no_hp' => trim($request->no_hp ?? ''),
                ];

                if ($request->filled('new_password')) {
                    if (! $request->filled('current_password')) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Password saat ini wajib diisi.',
                        ], 422);
                    }
                    if (! Hash::check($request->current_password, $satpam->password_hash)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Password saat ini salah.',
                        ], 422);
                    }
                    $updateData['password_hash'] = Hash::make($request->new_password);
                }

                if ($request->hasFile('foto')) {
                    $file = $request->file('foto');
                    $filename = 'profile_satpam_'.$satpam->id_satpam.'_'.time().'.'.$file->getClientOriginalExtension();
                    $destinationPath = public_path('uploads/profile');

                    if (! File::exists($destinationPath)) {
                        File::makeDirectory($destinationPath, 0755, true, true);
                    }

                    if (isset($satpam->foto_profil) && $satpam->foto_profil && File::exists(public_path($satpam->foto_profil))) {
                        @File::delete(public_path($satpam->foto_profil));
                    }

                    $file->move($destinationPath, $filename);
                    $updateData['foto_profil'] = 'uploads/profile/'.$filename;
                }

                $satpam->update($updateData);

                session(['auth_nama_guru' => $satpam->nama]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Profil satpam berhasil diperbarui!',
                    'foto_profil' => isset($satpam->foto_profil) && $satpam->foto_profil ? asset($satpam->foto_profil) : null,
                    'nama_guru' => $satpam->nama,
                    'username' => $satpam->username,
                ]);
            }

            // Otomatis pastikan kolom foto_profil ada di tabel guru
            if (! Schema::hasColumn('guru', 'foto_profil')) {
                try {
                    Schema::table('guru', function (Blueprint $table) {
                        $table->string('foto_profil', 255)->nullable();
                    });
                } catch (\Throwable $th) {
                    // Abaikan jika sudah ada
                }
            }

            $guruId = session('auth_guru_id');
            $guru = $guruId ? Guru::find($guruId) : Guru::first();

            if (! $guru) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User guru tidak ditemukan.',
                ], 404);
            }

            $request->validate([
                'nama_guru' => 'required|string|max:100',
                'username' => 'required|string|max:50|unique:guru,username,'.$guru->id_guru.',id_guru',
                'no_hp' => 'nullable|string|max:20',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'new_password' => 'nullable|string|min:4',
            ], [
                'nama_guru.required' => 'Nama lengkap wajib diisi.',
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
                'foto.image' => 'File harus berupa gambar.',
                'foto.max' => 'Ukuran foto maksimal 2MB.',
                'new_password.min' => 'Password baru minimal 4 karakter.',
            ]);

            $updateData = [
                'nama_guru' => trim($request->nama_guru),
                'username' => trim($request->username),
                'no_hp' => trim($request->no_hp),
            ];

            // Ganti Password jika diisi
            if ($request->filled('new_password')) {
                if (! $request->filled('current_password')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Password saat ini wajib diisi.',
                    ], 422);
                }
                if (! Hash::check($request->current_password, $guru->password_hash)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Password saat ini salah.',
                    ], 422);
                }
                $updateData['password_hash'] = Hash::make($request->new_password);
            }

            // Upload Foto Profil jika ada
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'profile_'.$guru->id_guru.'_'.time().'.'.$file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/profile');

                if (! File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }

                // Hapus foto lama jika ada
                if (isset($guru->foto_profil) && $guru->foto_profil && File::exists(public_path($guru->foto_profil))) {
                    @File::delete(public_path($guru->foto_profil));
                }

                $file->move($destinationPath, $filename);
                if (Schema::hasColumn('guru', 'foto_profil')) {
                    $updateData['foto_profil'] = 'uploads/profile/'.$filename;
                }
            }

            $guru->update($updateData);

            // Update session jika nama guru berubah
            session(['auth_nama_guru' => $guru->nama_guru]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profil & pengaturan berhasil diperbarui!',
                'foto_profil' => isset($guru->foto_profil) && $guru->foto_profil ? asset($guru->foto_profil) : null,
                'nama_guru' => $guru->nama_guru,
                'username' => $guru->username,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal simpan: '.$e->getMessage(),
            ], 500);
        }
    }
}

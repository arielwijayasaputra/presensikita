<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Mengelola data guru melalui operasi CRUD, impor bulk, serta pengalihan dan pengosongan jadwal mengajar.
 */
class GuruController extends Controller
{
    /**
     * Mengimpor data guru dari file spreadsheet lalu membuat atau memperbarui data guru berdasarkan NIP, username, atau nama.
     *
     * @return JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate(['file_guru' => ['required', 'file', 'max:25600', 'mimes:csv,txt,xlsx,xls']], [
            'file_guru.required' => 'File data guru wajib dipilih.',
            'file_guru.mimes' => 'Format file harus CSV, Excel, atau TXT.',
            'file_guru.max' => 'Ukuran file maksimal 25MB.',
        ]);

        $file = $request->file('file_guru');
        try {
            $rows = $this->readSpreadsheetRows($file->getRealPath());
        } catch (\Throwable $exception) {
            return response()->json(['status' => 'error', 'message' => 'File tidak dapat dibaca: '.$exception->getMessage()], 422);
        }

        $imported = 0;
        $updated = 0;
        $skipped = [];
        foreach ($rows as $line => $row) {
            $name = trim((string) ($row['nama_guru'] ?? ''));
            $nip = preg_replace('/\D+/', '', (string) ($row['nip'] ?? '')) ?: null;
            if ($name === '') {
                $skipped[] = 'Baris '.($line + 1).': nama guru kosong.';

                continue;
            }
            $requestedUsername = strtolower(trim((string) ($row['username'] ?? '')));
            $kodeMapel = trim((string) ($row['kode_mapel'] ?? ''));
            $mapel = $kodeMapel !== '' ? Mapel::where('kode_mapel', $kodeMapel)->first() : null;
            if ($kodeMapel !== '' && ! $mapel) {
                $skipped[] = 'Baris '.($line + 1).': kode mapel "'.$kodeMapel.'" tidak terdaftar.';

                continue;
            }
            $idMapel = $mapel?->id_mapel;
            $guru = $nip ? Guru::where('nip', $nip)->first() : null;
            $guru ??= $requestedUsername !== '' ? Guru::whereRaw('LOWER(username) = ?', [$requestedUsername])->first() : null;
            $guru ??= Guru::whereRaw('LOWER(TRIM(nama_guru)) = ?', [strtolower($name)])->first();
            if ($guru) {
                if ($guru->is_admin) {
                    $skipped[] = 'Baris '.($line + 1).": akun admin {$guru->username} dilewati agar username dan password tidak berubah.";

                    continue;
                }
                $guru->update([
                    'nip' => $guru->nip ?: $nip,
                    'nama_guru' => $name,
                    'Peran' => $row['peran'] ?: $guru->Peran,
                    'no_hp' => $row['no_hp'] ?: $guru->no_hp,
                    'id_mapel' => $idMapel ?: $guru->id_mapel,
                ]);
                $this->assignWaliKelas($guru, $row['peran'] ?? '', $row['nama_kelas'] ?? '');
                $updated++;

                continue;
            }
            $username = $this->uniqueUsername($requestedUsername, $name, $nip);
            $defaultPassword = $this->extractFirstName($name).'123';
            Guru::create([
                'nip' => $nip,
                'nama_guru' => $name,
                'Peran' => trim((string) ($row['peran'] ?? 'Guru')) ?: 'Guru',
                'no_hp' => trim((string) ($row['no_hp'] ?? '')) ?: null,
                'username' => $username,
                'password_hash' => Hash::make($defaultPassword),
                'is_admin' => 0,
                'is_aktif' => 1,
                'id_mapel' => $idMapel,
            ]);
            $guruBaru = Guru::where('username', $username)->first();
            $this->assignWaliKelas($guruBaru, $row['peran'] ?? '', $row['nama_kelas'] ?? '');
            $imported++;
        }

        if ($imported === 0 && $updated === 0) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data guru yang berhasil dibaca. Pastikan file berisi kolom judul yang didukung (NIP, Nama Guru, Peran/Jabatan, Kode Mapel, Wali Kelas, No HP, Username).'], 422);
        }

        return response()->json(['status' => 'success', 'message' => "Import selesai: {$imported} guru baru, {$updated} data diperbarui.", 'skipped' => $skipped]);
    }

    /**
     * Membaca baris data dari file spreadsheet dan memetakannya ke kolom standar berdasarkan header yang dikenali.
     */
    private function readSpreadsheetRows(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet()->toArray(null, true, true, false);
        if (count($sheet) < 1) {
            return [];
        }
        $headers = array_map(fn ($header) => $this->normalizeHeader((string) $header), array_shift($sheet));
        $aliases = [
            'nip' => ['nip', 'nuptk', 'nik'],
            'nama_guru' => ['nama', 'nama_guru', 'nama_lengkap', 'nama_pegawai'],
            'peran' => ['peran', 'jabatan', 'tugas_tambahan', 'role_jabatan', 'role', 'jabatan_tugas'],
            'nama_kelas' => ['nama_kelas', 'kelas', 'wali_kelas', 'kelas_wali'],
            'no_hp' => ['no_hp', 'no_telepon', 'telepon', 'hp', 'handphone', 'no_handphone', 'no_handfone', 'handfone'],
            'username' => ['username', 'user', 'akun'],
            'kode_mapel' => ['kode_mapel', 'kd_mapel', 'kode', 'kode_mapel'],
        ];
        $map = [];
        foreach ($aliases as $field => $names) {
            foreach ($headers as $index => $header) {
                if (in_array($header, $names, true)) {
                    $map[$field] = $index;
                    break;
                }
            }
        }

        return array_values(array_map(function ($row) use ($map, $aliases) {
            $result = [];
            foreach (array_keys($aliases) as $field) {
                $idx = $map[$field] ?? null;
                $result[$field] = ($idx !== null && isset($row[$idx])) ? trim((string) $row[$idx]) : '';
            }

            return $result;
        }, $sheet));
    }

    /**
     * Menormalisasi header kolom menjadi format snake_case huruf kecil.
     */
    private function normalizeHeader(string $header): string
    {
        return preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($header)));
    }

    /**
     * Menugaskan guru sebagai wali kelas jika peran mengandung "wali kelas" dan nama kelas valid.
     */
    private function assignWaliKelas(?Guru $guru, string $peran, string $namaKelas): void
    {
        if (! $guru || stripos($peran, 'wali kelas') === false || trim($namaKelas) === '' || trim($namaKelas) === '-') {
            return;
        }
        $kelas = Kelas::whereRaw('LOWER(TRIM(nama_kelas)) = ?', [strtolower(trim($namaKelas))])->first();
        if ($kelas) {
            $kelas->update(['id_wali_kelas' => $guru->id_guru]);
        }
    }

    /**
     * Mengambil nama depan dari nama lengkap, melewati gelar/titel di awal.
     */
    private function extractFirstName(string $fullName): string
    {
        $titles = ['dra', 'drs', 'dr', 'prof', 'ir', 'pdt', 'hj', 'h'];
        // Ambil bagian sebelum koma (gelar belakang)
        $beforeComma = explode(',', $fullName)[0];
        $parts = preg_split('/\s+/', trim($beforeComma));
        foreach ($parts as $part) {
            $clean = strtolower(preg_replace('/[^a-zA-Z]/', '', $part));
            if ($clean !== '' && strlen($clean) > 1 && ! in_array($clean, $titles, true)) {
                return $clean;
            }
        }

        return strtolower(preg_replace('/[^a-z0-9]/i', '', $parts[0] ?? 'guru')) ?: 'guru';
    }

    /**
     * Menghasilkan username unik dari nama depan, dengan suffix numerik jika sudah ada di database.
     */
    private function uniqueUsername(string $requested, string $name, ?string $nip): string
    {
        if ($requested !== '') {
            $base = strtolower(preg_replace('/[^a-z0-9]/i', '', $requested));
        } else {
            $base = $this->extractFirstName($name);
        }
        $base = $base ?: 'guru'.($nip ?: time());
        $username = $base;
        $counter = 2;
        while (Guru::where('username', $username)->exists()) {
            $username = $base.$counter++;
        }

        return $username;
    }

    /**
     * Menyimpan data guru baru berdasarkan input dari request.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required|string',
            'username' => 'required|string|unique:guru,username',
            'password' => 'required|string|min:4',
        ], [
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan guru lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
        ]);

        $guru = Guru::create([
            'nip' => $request->nip ?: null,
            'nama_guru' => $request->nama_guru,
            'Peran' => $request->peran ?? 'Guru',
            'no_hp' => $request->no_hp ?: null,
            'username' => strtolower(trim($request->username)),
            'password_hash' => Hash::make($request->password),
            'is_admin' => 0,
            'is_aktif' => 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Guru berhasil ditambahkan!',
            'data' => $guru,
        ]);
    }

    /**
     * Memperbarui data guru yang sudah ada, termasuk password jika disertakan.
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        if ($guru->is_admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun admin hanya dapat diubah oleh admin tersebut melalui menu Profil.',
            ], 403);
        }

        $request->validate([
            'nama_guru' => 'required|string',
            'username' => 'required|string|unique:guru,username,'.$guru->id_guru.',id_guru',
        ], [
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan guru lain.',
        ]);

        $data = [
            'nip' => $request->nip ?: null,
            'nama_guru' => $request->nama_guru,
            'Peran' => $request->peran ?? 'Guru',
            'no_hp' => $request->no_hp ?: null,
            'username' => strtolower(trim($request->username)),
            'is_admin' => 0,
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $guru->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data guru berhasil diperbarui!',
            'data' => $guru,
        ]);
    }

    /**
     * Mengaktifkan atau menonaktifkan status akun guru, dengan pengecekan jadwal mengajar aktif.
     *
     * @return JsonResponse
     */
    public function toggleAktif($id)
    {
        $guru = Guru::findOrFail($id);

        if (! session('auth_is_admin') && $guru->id_guru == session('auth_guru_id')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri!',
            ], 422);
        }

        // Jika guru sedang aktif dan akan dinonaktifkan, periksa apakah masih punya jadwal mengajar aktif
        if ($guru->is_aktif) {
            $jadwalCount = JadwalMengajar::where('id_guru', $guru->id_guru)->count();
            if ($jadwalCount > 0) {
                return response()->json([
                    'status' => 'has_jadwal',
                    'message' => "Guru {$guru->nama_guru} masih memiliki {$jadwalCount} jadwal mengajar aktif. Anda harus mengalihkan atau mengosongkan jadwal terlebih dahulu.",
                    'jadwal_count' => $jadwalCount,
                    'guru_id' => $guru->id_guru,
                    'nama_guru' => $guru->nama_guru,
                ], 422);
            }
        }

        $guru->is_aktif = $guru->is_aktif ? 0 : 1;
        $guru->save();

        return response()->json([
            'status' => 'success',
            'message' => $guru->is_aktif ? 'Guru berhasil diaktifkan!' : 'Guru berhasil dinonaktifkan!',
            'is_aktif' => $guru->is_aktif,
        ]);
    }

    /**
     * Menghapus semua data guru secara permanen, mengecualikan akun admin yang sedang login.
     *
     * @return JsonResponse
     */
    public function hapusSemua(Request $request)
    {
        $selfId = session('auth_guru_id');

        $query = Guru::query();
        if ($selfId) {
            $query->where('id_guru', '!=', $selfId);
        }
        $targetIds = $query->pluck('id_guru')->toArray();
        $total = count($targetIds);
        if ($total === 0) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data guru untuk dihapus.'], 422);
        }

        DB::transaction(function () use ($targetIds, $selfId) {
            // 1. Kosongkan relasi wali kelas di tabel kelas untuk guru yang akan dihapus
            Kelas::whereIn('id_wali_kelas', $targetIds)->update(['id_wali_kelas' => null]);

            // 2. Kosongkan relasi guru di jadwal mengajar
            JadwalMengajar::whereIn('id_guru', $targetIds)->update(['id_guru' => null]);

            // 3. Hapus permanen data guru
            if ($selfId) {
                Guru::where('id_guru', '!=', $selfId)->forceDelete();
            } else {
                Guru::query()->forceDelete();
            }
        });

        $message = $selfId
            ? "Semua data guru berhasil dihapus permanen ($total guru), kecuali akun admin yang sedang login."
            : "Semua data guru berhasil dihapus permanen ($total guru).";

        return response()->json(['status' => 'success', 'message' => $message, 'deleted' => $total]);
    }

    /**
     * Menghapus data guru berdasarkan ID, dengan pelepasan relasi wali kelas dan pengecekan jadwal aktif.
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);

        if (! session('auth_is_admin') && $guru->id_guru == session('auth_guru_id')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri!',
            ], 422);
        }

        $jadwalCount = JadwalMengajar::where('id_guru', $guru->id_guru)->count();
        if ($jadwalCount > 0) {
            return response()->json([
                'status' => 'has_jadwal',
                'message' => "Guru {$guru->nama_guru} tidak dapat dihapus karena masih memiliki {$jadwalCount} jadwal mengajar aktif. Anda harus mengalihkan atau mengosongkan jadwal terlebih dahulu.",
                'jadwal_count' => $jadwalCount,
                'guru_id' => $guru->id_guru,
                'nama_guru' => $guru->nama_guru,
            ], 422);
        }

        DB::transaction(function () use ($guru) {
            // Lepaskan status wali kelas di tabel kelas jika guru ini adalah wali kelas
            Kelas::where('id_wali_kelas', $guru->id_guru)->update(['id_wali_kelas' => null]);

            $guru->delete();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data guru berhasil dihapus!',
        ]);
    }

    /**
     * Memindahkan semua jadwal mengajar dari satu guru ke guru pengganti yang aktif.
     *
     * @return JsonResponse
     */
    public function alihkanJadwal(Request $request, $id)
    {
        $guruAsal = Guru::findOrFail($id);

        $request->validate([
            'id_guru_tujuan' => ['required', 'integer', 'exists:guru,id_guru'],
        ], [
            'id_guru_tujuan.required' => 'Pilih guru pengganti terlebih dahulu.',
            'id_guru_tujuan.exists' => 'Guru pengganti tidak ditemukan.',
        ]);

        $idTujuan = (int) $request->id_guru_tujuan;
        if ($idTujuan === (int) $id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Guru pengganti harus berbeda dari guru asal.',
            ], 422);
        }

        $guruTujuan = Guru::where('id_guru', $idTujuan)->where('is_aktif', 1)->firstOrFail();

        $count = JadwalMengajar::where('id_guru', $guruAsal->id_guru)->count();
        if ($count === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Guru ini tidak memiliki jadwal mengajar aktif untuk dialihkan.',
            ], 422);
        }

        JadwalMengajar::where('id_guru', $guruAsal->id_guru)->update([
            'id_guru' => $guruTujuan->id_guru,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Sebanyak {$count} jadwal mengajar berhasil dialihkan ke {$guruTujuan->nama_guru}.",
            'transferred_count' => $count,
            'guru_tujuan' => $guruTujuan->nama_guru,
        ]);
    }

    /**
     * Mengosongkan semua jadwal mengajar guru dengan menyetel id_guru ke null.
     *
     * @return JsonResponse
     */
    public function kosongkanJadwal($id)
    {
        $guru = Guru::findOrFail($id);

        $count = JadwalMengajar::where('id_guru', $guru->id_guru)->count();
        if ($count === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Guru ini tidak memiliki jadwal mengajar aktif.',
            ], 422);
        }

        JadwalMengajar::where('id_guru', $guru->id_guru)->update([
            'id_guru' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Sebanyak {$count} jadwal mengajar telah dikosongkan (status: Belum Ada Pengampu). Jadwal dapat diganti ke guru lain di menu Jadwal Mengajar.",
            'cleared_count' => $count,
        ]);
    }
}

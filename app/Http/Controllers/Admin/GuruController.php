<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlihkanJadwalRequest;
use App\Http\Requests\Admin\DeleteSelectedGuruRequest;
use App\Http\Requests\Admin\ImportGuruRequest;
use App\Http\Requests\Admin\StoreGuruRequest;
use App\Http\Requests\Admin\UpdateGuruRequest;
use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\Kelas;
use App\Services\Import\GuruImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Mengelola data guru melalui operasi CRUD, impor bulk, serta pengalihan dan pengosongan jadwal mengajar.
 */
class GuruController extends Controller
{
    public function __construct(private GuruImportService $guruImportService) {}

    /**
     * Mengimpor data guru dari file spreadsheet lalu membuat atau memperbarui data guru berdasarkan NIP, username, atau nama.
     *
     * @return JsonResponse
     */
    public function import(ImportGuruRequest $request)
    {
        return $this->guruImportService->import($request->file('file_guru'));
    }

    /**
     * Menyimpan data guru baru berdasarkan input dari request.
     *
     * @return JsonResponse
     */
    public function store(StoreGuruRequest $request)
    {
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
    public function update(UpdateGuruRequest $request, $id)
    {
        $guru = Guru::findOrFail($id);

        if ($guru->is_admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun admin hanya dapat diubah oleh admin tersebut melalui menu Profil.',
            ], 403);
        }

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

    public function hapusTerpilih(DeleteSelectedGuruRequest $request): JsonResponse
    {
        $data = $request->validated();

        $ids = array_values(array_unique(array_map('intval', $data['ids'])));
        $selfId = (int) session('auth_guru_id', 0);

        if ($selfId > 0 && in_array($selfId, $ids, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Guru yang sedang digunakan untuk login tidak dapat dihapus. Hapus guru tersebut dari pilihan.',
            ], 422);
        }

        $gurus = Guru::whereIn('id_guru', $ids)->get();
        if ($gurus->count() !== count($ids)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sebagian data guru tidak ditemukan atau sudah dihapus.',
            ], 422);
        }

        $scheduleCounts = JadwalMengajar::whereIn('id_guru', $ids)
            ->selectRaw('id_guru, COUNT(*) as jumlah')
            ->groupBy('id_guru')
            ->pluck('jumlah', 'id_guru');
        $clearedCount = (int) $scheduleCounts->sum();

        DB::transaction(function () use ($ids) {
            Kelas::whereIn('id_wali_kelas', $ids)->update(['id_wali_kelas' => null]);
            JadwalMengajar::whereIn('id_guru', $ids)->update(['id_guru' => null]);
            Guru::whereIn('id_guru', $ids)->delete();
        });

        $message = $clearedCount > 0
            ? 'Sebanyak '.count($ids)." data guru berhasil dihapus dan {$clearedCount} jadwal mengajar dilepas dari guru."
            : 'Sebanyak '.count($ids).' data guru berhasil dihapus.';

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'deleted_count' => count($ids),
            'cleared_count' => $clearedCount,
        ]);
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
    public function alihkanJadwal(AlihkanJadwalRequest $request, $id)
    {
        $guruAsal = Guru::findOrFail($id);

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

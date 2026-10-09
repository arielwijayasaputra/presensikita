<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariKhusus;
use App\Services\HariKhususService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HariKhususController extends Controller
{
    /**
     * Menyimpan data Hari Khusus baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipe' => ['required', 'in:event,pulang_cepat'],
            'judul' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tingkat' => ['required', 'array', 'min:1'],
            'tingkat.*' => ['in:10,11,12,"10","11","12"'],
            'jam_pulang' => ['nullable', 'required_if:tipe,pulang_cepat'],
            'aturan_presensi' => ['nullable', 'required_if:tipe,event', 'in:tetap_wajib,diliburkan,hadir_event'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], [
            'tipe.required' => 'Pilih jenis hari khusus (Event atau Pulang Cepat).',
            'judul.required' => 'Judul atau keterangan hari khusus wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'tingkat.required' => 'Pilih minimal satu tingkat kelas (10, 11, atau 12).',
            'jam_pulang.required_if' => 'Jam kepulangan wajib diisi untuk jenis Pulang Cepat.',
            'aturan_presensi.required_if' => 'Aturan presensi wajib dipilih untuk jenis Event.',
        ]);

        $tingkatList = array_map('intval', $validated['tingkat']);

        // Validasi irisan bentrok
        $conflictError = HariKhususService::checkConflict(
            $validated['tanggal_mulai'],
            $validated['tanggal_selesai'],
            $tingkatList
        );

        if ($conflictError) {
            return response()->json([
                'status' => 'error',
                'message' => $conflictError,
            ], 422);
        }

        $adminId = session('auth_admin_id') ?? session('auth_guru_id');

        $hariKhusus = HariKhusus::create([
            'tipe' => $validated['tipe'],
            'judul' => $validated['judul'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'tingkat' => $tingkatList,
            'jam_pulang' => $validated['tipe'] === 'pulang_cepat' ? substr($validated['jam_pulang'], 0, 5) . ':00' : null,
            'aturan_presensi' => $validated['tipe'] === 'event' ? $validated['aturan_presensi'] : null,
            'keterangan' => $validated['keterangan'] ?? null,
            'created_by' => $adminId,
            'updated_by' => $adminId,
        ]);

        HariKhususService::clearCache();

        if ($hariKhusus->tipe === 'event' && $hariKhusus->aturan_presensi === 'hadir_event') {
            app(\App\Services\AbsensiService::class)->syncEventHadir($hariKhusus);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Hari khusus berhasil ditambahkan.',
            'data' => $hariKhusus,
        ]);
    }

    /**
     * Memperbarui data Hari Khusus.
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $hariKhusus = HariKhusus::findOrFail($id);

        $validated = $request->validate([
            'tipe' => ['required', 'in:event,pulang_cepat'],
            'judul' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tingkat' => ['required', 'array', 'min:1'],
            'tingkat.*' => ['in:10,11,12,"10","11","12"'],
            'jam_pulang' => ['nullable', 'required_if:tipe,pulang_cepat'],
            'aturan_presensi' => ['nullable', 'required_if:tipe,event', 'in:tetap_wajib,diliburkan,hadir_event'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], [
            'tipe.required' => 'Pilih jenis hari khusus (Event atau Pulang Cepat).',
            'judul.required' => 'Judul atau keterangan hari khusus wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'tingkat.required' => 'Pilih minimal satu tingkat kelas (10, 11, atau 12).',
            'jam_pulang.required_if' => 'Jam kepulangan wajib diisi untuk jenis Pulang Cepat.',
            'aturan_presensi.required_if' => 'Aturan presensi wajib dipilih untuk jenis Event.',
        ]);

        $tingkatList = array_map('intval', $validated['tingkat']);

        // Validasi irisan bentrok abaikan ID saat ini
        $conflictError = HariKhususService::checkConflict(
            $validated['tanggal_mulai'],
            $validated['tanggal_selesai'],
            $tingkatList,
            (int) $id
        );

        if ($conflictError) {
            return response()->json([
                'status' => 'error',
                'message' => $conflictError,
            ], 422);
        }

        $adminId = session('auth_admin_id') ?? session('auth_guru_id');

        $hariKhusus->update([
            'tipe' => $validated['tipe'],
            'judul' => $validated['judul'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'tingkat' => $tingkatList,
            'jam_pulang' => $validated['tipe'] === 'pulang_cepat' ? substr($validated['jam_pulang'], 0, 5) . ':00' : null,
            'aturan_presensi' => $validated['tipe'] === 'event' ? $validated['aturan_presensi'] : null,
            'keterangan' => $validated['keterangan'] ?? null,
            'updated_by' => $adminId,
        ]);

        HariKhususService::clearCache();

        if ($hariKhusus->tipe === 'event' && $hariKhusus->aturan_presensi === 'hadir_event') {
            app(\App\Services\AbsensiService::class)->syncEventHadir($hariKhusus);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Hari khusus berhasil diperbarui.',
            'data' => $hariKhusus,
        ]);
    }

    /**
     * Menghapus data Hari Khusus.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $hariKhusus = HariKhusus::findOrFail($id);
        $hariKhusus->delete();

        HariKhususService::clearCache();

        return response()->json([
            'status' => 'success',
            'message' => 'Hari khusus berhasil dihapus.',
        ]);
    }
}

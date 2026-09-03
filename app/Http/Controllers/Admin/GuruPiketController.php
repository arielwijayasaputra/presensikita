<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruPiket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Mengelola penugasan guru piket harian.
 */
class GuruPiketController extends Controller
{
    /**
     * Menyimpan penugasan guru piket untuk satu tanggal.
     *
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'guru_ids' => ['nullable', 'array'],
            'guru_ids.*' => ['integer', 'distinct', 'exists:guru,id_guru'],
        ]);

        $guruIds = Guru::whereIn('id_guru', $data['guru_ids'] ?? [])
            ->where('is_admin', 0)
            ->where('is_aktif', 1)
            ->pluck('id_guru')
            ->all();

        DB::transaction(function () use ($data, $guruIds) {
            GuruPiket::withTrashed()->whereDate('tanggal', $data['tanggal'])->forceDelete();

            foreach ($guruIds as $guruId) {
                GuruPiket::create([
                    'id_guru' => $guruId,
                    'tanggal' => $data['tanggal'],
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => count($guruIds).' guru piket berhasil disimpan untuk tanggal '.date('d-m-Y', strtotime($data['tanggal'])).'.',
        ]);
    }

    /**
     * Menyimpan penugasan guru piket untuk beberapa tanggal sekaligus.
     *
     * @return JsonResponse
     */
    public function updateBulk(Request $request)
    {
        $raw = $request->input('assignments', []);
        $filtered = [];
        foreach ($raw as $tanggal => $guruList) {
            $clean = array_values(array_filter((array) $guruList, fn ($v) => $v !== '' && $v !== null));
            if (! empty($clean)) {
                $filtered[$tanggal] = $clean;
            }
        }
        $request->merge(['assignments' => $filtered]);

        $data = $request->validate([
            'assignments' => ['required', 'array'],
            'assignments.*' => ['nullable', 'array'],
            'assignments.*.*' => ['integer', 'exists:guru,id_guru'],
        ]);

        // Check for duplicate guru per day
        foreach ($data['assignments'] as $tanggal => $guruList) {
            if (count($guruList) !== count(array_unique($guruList))) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Guru tidak boleh dobel di hari yang sama (tanggal $tanggal).",
                ], 422);
            }
        }

        $guruIds = Guru::where('is_admin', 0)
            ->where('is_aktif', 1)
            ->pluck('id_guru')
            ->all();

        $totalAssigned = 0;

        DB::transaction(function () use ($data, $guruIds, &$totalAssigned) {
            foreach ($data['assignments'] as $tanggal => $guruList) {
                if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                    continue;
                }

                $validGurus = array_intersect($guruList ?? [], $guruIds);

                GuruPiket::withTrashed()->whereDate('tanggal', $tanggal)->forceDelete();

                foreach ($validGurus as $guruId) {
                    GuruPiket::create([
                        'id_guru' => $guruId,
                        'tanggal' => $tanggal,
                    ]);
                    $totalAssigned++;
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Penugasan guru piket berhasil disimpan untuk '.count($data['assignments']).' hari ('.$totalAssigned.' penugasan total).',
        ]);
    }
}

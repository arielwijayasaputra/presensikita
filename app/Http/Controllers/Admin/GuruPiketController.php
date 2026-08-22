<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruPiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruPiketController extends Controller
{
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
            GuruPiket::whereDate('tanggal', $data['tanggal'])->delete();

            foreach ($guruIds as $guruId) {
                GuruPiket::create([
                    'id_guru' => $guruId,
                    'tanggal' => $data['tanggal'],
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => count($guruIds) . ' guru piket berhasil disimpan untuk tanggal ' . date('d-m-Y', strtotime($data['tanggal'])) . '.',
        ]);
    }
}

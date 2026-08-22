<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JamPelajaranController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'jam' => ['required', 'array'],
            'jam.*.jam_mulai' => ['required', 'date_format:H:i'],
            'jam.*.jam_selesai' => ['required', 'date_format:H:i', 'after:jam.*.jam_mulai'],
        ], [
            'jam.*.jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam.*.jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam.*.jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['jam'] as $idJam => $waktu) {
                JamPelajaran::where('id_jam', $idJam)->update([
                    'jam_mulai' => $waktu['jam_mulai'],
                    'jam_selesai' => $waktu['jam_selesai'],
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Jam pelajaran berhasil diperbarui.',
        ]);
    }
}

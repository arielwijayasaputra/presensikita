<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran;
use App\Models\Pengaturan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Mengelola pengaturan jam pelajaran dan waktu istirahat.
 */
class JamPelajaranController extends Controller
{
    /**
     * Memperbarui jam mulai dan jam selesai untuk satu jam pelajaran pada hari-hari tertentu.
     *
     * @return JsonResponse
     */
    public function updateSingle(Request $request, int $id)
    {
        $data = $request->validate([
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);
        $source = JamPelajaran::findOrFail($id);
        $hariPilihan = $request->input('hari', [$source->hari]);
        $hariPilihan = array_values(array_intersect($hariPilihan, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']));
        foreach ($hariPilihan as $hari) {
            $jamKe = (int) $source->jam_ke;
            if ($hari === 'Jumat' && $jamKe < 100) {
                $jamKe += 100;
            }
            if ($hari !== 'Jumat' && $jamKe >= 100) {
                $jamKe -= 100;
            }
            JamPelajaran::where('hari', $hari)->where('jam_ke', $jamKe)->update($data);
        }

        return response()->json(['status' => 'success', 'message' => 'Jam pelajaran berhasil diperbarui.']);
    }

    /**
     * Memperbarui waktu istirahat ke-1 atau ke-2 untuk hari tertentu.
     *
     * @return JsonResponse
     */
    public function updateIstirahat(Request $request, string $hari, int $nomor)
    {
        abort_unless(in_array($hari, ['weekday', 'friday'], true) && in_array($nomor, [1, 2], true), 404);
        $data = $request->validate([
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);
        $hariPilihan = $request->input('hari', [$hari]);
        foreach (array_unique($hariPilihan) as $hariDipilih) {
            $prefix = $hariDipilih === 'friday' || $hariDipilih === 'Jumat' ? 'jam_istirahat_jumat_' : 'jam_istirahat_';
            Pengaturan::set($prefix.$nomor.'_mulai', $data['jam_mulai']);
            Pengaturan::set($prefix.$nomor.'_selesai', $data['jam_selesai']);
        }

        return response()->json(['status' => 'success', 'message' => 'Waktu istirahat berhasil diperbarui.']);
    }

    /**
     * Memperbarui seluruh data jam pelajaran dan waktu istirahat sekaligus.
     *
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'jam' => ['required', 'array'],
            'jam.*.jam_mulai' => ['required', 'date_format:H:i'],
            'jam.*.jam_selesai' => ['required', 'date_format:H:i', 'after:jam.*.jam_mulai'],
            'istirahat.1.mulai' => ['required', 'date_format:H:i'],
            'istirahat.1.selesai' => ['required', 'date_format:H:i', 'after:istirahat.1.mulai'],
            'istirahat.2.mulai' => ['required', 'date_format:H:i'],
            'istirahat.2.selesai' => ['required', 'date_format:H:i', 'after:istirahat.2.mulai'],
            'istirahat_jumat.1.mulai' => ['required', 'date_format:H:i'],
            'istirahat_jumat.1.selesai' => ['required', 'date_format:H:i', 'after:istirahat_jumat.1.mulai'],
            'istirahat_jumat.2.mulai' => ['required', 'date_format:H:i'],
            'istirahat_jumat.2.selesai' => ['required', 'date_format:H:i', 'after:istirahat_jumat.2.mulai'],
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

            foreach (['1', '2'] as $nomor) {
                Pengaturan::set("jam_istirahat_{$nomor}_mulai", $data['istirahat'][$nomor]['mulai']);
                Pengaturan::set("jam_istirahat_{$nomor}_selesai", $data['istirahat'][$nomor]['selesai']);
                Pengaturan::set("jam_istirahat_jumat_{$nomor}_mulai", $data['istirahat_jumat'][$nomor]['mulai']);
                Pengaturan::set("jam_istirahat_jumat_{$nomor}_selesai", $data['istirahat_jumat'][$nomor]['selesai']);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Jam pelajaran berhasil diperbarui.',
        ]);
    }
}

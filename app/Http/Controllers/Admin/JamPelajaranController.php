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
     * Menambahkan jam pelajaran baru untuk hari-hari tertentu.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'jam_ke' => ['required', 'integer', 'min:1'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'hari' => ['required', 'array', 'min:1'],
            'hari.*' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
        ], [
            'jam_ke.required' => 'Nomor jam pelajaran (jam ke-) wajib diisi.',
            'jam_ke.integer' => 'Nomor jam pelajaran harus berupa angka.',
            'jam_ke.min' => 'Nomor jam pelajaran minimal 1.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'hari.required' => 'Pilih minimal satu hari.',
            'hari.min' => 'Pilih minimal satu hari.',
        ]);

        $inputJamKe = (int) $data['jam_ke'];
        $hariList = array_values(array_unique($data['hari']));

        // Peringatan jika jam sudah ada pada hari yang dipilih
        foreach ($hariList as $hari) {
            $internalJamKe = $hari === 'Jumat'
                ? ($inputJamKe < 100 ? $inputJamKe + 100 : $inputJamKe)
                : ($inputJamKe >= 100 ? $inputJamKe - 100 : $inputJamKe);

            $exists = JamPelajaran::where('hari', $hari)
                ->where('jam_ke', $internalJamKe)
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Jam ke-{$inputJamKe} pada hari {$hari} sudah ada sebelumnya. Silakan gunakan nomor jam lain atau sesuaikan jadwal yang sudah ada.",
                ], 422);
            }
        }

        DB::transaction(function () use ($data, $inputJamKe, $hariList) {
            foreach ($hariList as $hari) {
                $internalJamKe = $hari === 'Jumat'
                    ? ($inputJamKe < 100 ? $inputJamKe + 100 : $inputJamKe)
                    : ($inputJamKe >= 100 ? $inputJamKe - 100 : $inputJamKe);

                $existing = JamPelajaran::withTrashed()
                    ->where('hari', $hari)
                    ->where('jam_ke', $internalJamKe)
                    ->first();

                if ($existing) {
                    $existing->deleted_at = null;
                    $existing->jam_mulai = $data['jam_mulai'];
                    $existing->jam_selesai = $data['jam_selesai'];
                    $existing->save();
                } else {
                    JamPelajaran::create([
                        'hari' => $hari,
                        'jam_ke' => $internalJamKe,
                        'jam_mulai' => $data['jam_mulai'],
                        'jam_selesai' => $data['jam_selesai'],
                    ]);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => "Jam pelajaran ke-{$inputJamKe} berhasil ditambahkan ke hari: " . implode(', ', $hariList) . '.',
        ]);
    }

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
        $hariPilihan = array_values(array_intersect($hariPilihan, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']));
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

    /**
     * Menghapus satu jam pelajaran berdasarkan ID.
     *
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        $jam = JamPelajaran::findOrFail($id);
        $displayJam = $jam->jam_ke >= 100 ? $jam->jam_ke - 100 : $jam->jam_ke;
        $hari = $jam->hari ?? 'Hari';

        $usedInJadwal = DB::table('jadwal_mengajar')
            ->where('id_jam', $jam->id_jam)
            ->whereNull('deleted_at')
            ->count();

        if ($usedInJadwal > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Jam ke-{$displayJam} ({$hari}) masih digunakan oleh {$usedInJadwal} jadwal mengajar aktif. Harap pindahkan atau hapus jadwal tersebut terlebih dahulu.",
            ], 422);
        }

        $jam->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Jam ke-{$displayJam} pada hari {$hari} berhasil dihapus.",
        ]);
    }

    /**
     * Menghapus seluruh jam pelajaran pada hari tertentu.
     *
     * @return JsonResponse
     */
    public function destroyDay(string $hari)
    {
        abort_unless(in_array($hari, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'], true), 404);

        $jamIds = JamPelajaran::where('hari', $hari)->pluck('id_jam');
        $usedInJadwal = DB::table('jadwal_mengajar')
            ->whereIn('id_jam', $jamIds)
            ->whereNull('deleted_at')
            ->count();

        if ($usedInJadwal > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Hari {$hari} memiliki {$usedInJadwal} jadwal mengajar aktif yang masih menggunakan jam pelajarannya. Harap hapus atau pindahkan jadwal mengajar terlebih dahulu.",
            ], 422);
        }

        $deleted = JamPelajaran::where('hari', $hari)->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Seluruh jam pelajaran pada hari {$hari} ({$deleted} jam) berhasil dihapus.",
        ]);
    }

    /**
     * Menghapus waktu istirahat (mengosongkan waktu mulai dan selesai).
     *
     * @return JsonResponse
     */
    public function destroyIstirahat(string $hari, int $nomor)
    {
        abort_unless(in_array($hari, ['weekday', 'friday', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'], true) && in_array($nomor, [1, 2], true), 404);
        $prefix = ($hari === 'friday' || $hari === 'Jumat') ? 'jam_istirahat_jumat_' : 'jam_istirahat_';
        Pengaturan::set($prefix.$nomor.'_mulai', '');
        Pengaturan::set($prefix.$nomor.'_selesai', '');

        return response()->json([
            'status' => 'success',
            'message' => "Waktu Istirahat {$nomor} berhasil dihapus.",
        ]);
    }
}

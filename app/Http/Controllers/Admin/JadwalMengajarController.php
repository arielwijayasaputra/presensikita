<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportJadwalRequest;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\JadwalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Services\Import\JadwalImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mengelola jadwal mengajar guru melalui operasi CRUD, impor bulk, serta penugasan dan ketersediaan guru.
 */
class JadwalMengajarController extends Controller
{
    public function __construct(private JadwalImportService $jadwalImportService) {}

    /**
     * Mengimpor data jadwal mengajar dari file spreadsheet lalu membuat atau memperbarui jadwal berdasarkan hari, jam, dan kelas.
     *
     * @return JsonResponse
     */
    public function import(ImportJadwalRequest $request)
    {
        return $this->jadwalImportService->import($request->file('file_jadwal'));
    }

    /**
     * Menentukan daftar guru yang tersedia atau yang sedang mengajar (bentrok) pada hari dan jam tertentu.
     *
     * @return JsonResponse
     */
    public function guruTersedia(Request $request)
    {
        $request->validate([
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
            'id_jam' => ['required', 'integer'],
            'id_jadwal' => ['nullable', 'integer'],
        ]);

        $hari = $request->hari;
        $idJam = (int) $request->id_jam;
        $idJadwal = $request->id_jadwal ? (int) $request->id_jadwal : null;

        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $tahunId = $tahunAjaran?->id_tahun_ajaran ?? 1;

        // Ambil semua jadwal pada hari & jam tersebut yang sudah punya guru
        $bentrokJadwals = JadwalMengajar::where('jadwal_mengajar.hari', $hari)
            ->where('jadwal_mengajar.id_jam', $idJam)
            ->where('jadwal_mengajar.id_tahun_ajaran', $tahunId)
            ->whereNotNull('jadwal_mengajar.id_guru')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->when($idJadwal, fn ($q) => $q->where('jadwal_mengajar.id_jadwal', '!=', $idJadwal))
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->whereNull('kelas.deleted_at')
            ->whereNull('mapel.deleted_at')
            ->select(
                'jadwal_mengajar.id_guru',
                'kelas.nama_kelas',
                'mapel.nama_mapel'
            )
            ->get()
            ->keyBy('id_guru');

        $bentrokGuruIds = $bentrokJadwals->keys()->all();

        $allGuru = Guru::where('is_admin', 0)
            ->where('is_aktif', 1)
            ->orderBy('nama_guru')
            ->get();

        $guruTersedia = [];
        $guruBentrok = [];

        foreach ($allGuru as $g) {
            if (in_array($g->id_guru, $bentrokGuruIds)) {
                $infoBentrok = $bentrokJadwals->get($g->id_guru);
                $guruBentrok[] = [
                    'id_guru' => $g->id_guru,
                    'nama_guru' => $g->nama_guru,
                    'peran' => $g->Peran ?? 'Guru',
                    'status_jam' => 'bentrok',
                    'keterangan' => "Sedang Mengajar {$infoBentrok->nama_mapel} di {$infoBentrok->nama_kelas}",
                ];
            } else {
                $guruTersedia[] = [
                    'id_guru' => $g->id_guru,
                    'nama_guru' => $g->nama_guru,
                    'peran' => $g->Peran ?? 'Guru',
                    'status_jam' => 'tersedia',
                    'keterangan' => 'Jam Mengajar Kosong / Siap Ditugaskan',
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'hari' => $hari,
            'id_jam' => $idJam,
            'total_guru_tersedia' => count($guruTersedia),
            'total_guru_bentrok' => count($guruBentrok),
            'guru_tersedia' => $guruTersedia,
            'guru_bentrok' => $guruBentrok,
        ]);
    }

    /**
     * Menugaskan seorang guru pengampu untuk satu jadwal, dengan pengecekan jadwal bentrok pada hari dan jam yang sama.
     *
     * @return JsonResponse
     */
    public function tugaskanGuru(Request $request, $id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);

        if ($jadwal->isUpacara()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal upacara tidak memerlukan guru pengajar.',
            ], 422);
        }

        $request->validate([
            'id_guru' => ['required', 'integer', 'exists:guru,id_guru'],
        ], [
            'id_guru.required' => 'Pilih guru pengampu terlebih dahulu.',
            'id_guru.exists' => 'Guru pengampu tidak ditemukan.',
        ]);

        $guru = Guru::where('id_guru', $request->id_guru)->where('is_aktif', 1)->firstOrFail();

        $tahunId = $jadwal->id_tahun_ajaran;

        // Cek apakah guru ini sedang mengajar di jam dan hari yang sama di kelas lain
        $bentrok = JadwalMengajar::where('hari', $jadwal->hari)
            ->where('id_jam', $jadwal->id_jam)
            ->where('id_tahun_ajaran', $tahunId)
            ->where('id_guru', $guru->id_guru)
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->whereNull('deleted_at')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->select('kelas.nama_kelas')
            ->first();

        if ($bentrok) {
            return response()->json([
                'status' => 'error',
                'message' => "Guru {$guru->nama_guru} tidak dapat ditugaskan karena sudah memiliki jadwal mengajar di {$bentrok->nama_kelas} pada hari {$jadwal->hari} jam ke-{$jadwal->id_jam}.",
            ], 422);
        }

        $jadwal->update([
            'id_guru' => $guru->id_guru,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Guru {$guru->nama_guru} berhasil ditugaskan untuk jadwal ini.",
            'nama_guru' => $guru->nama_guru,
        ]);
    }

    /**
     * Memperbarui data jadwal mengajar berdasarkan input yang divalidasi.
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);
        $mapel = Mapel::findOrFail($request->id_mapel);
        $isUpacara = $mapel->isUpacara();

        $data = $request->validate([
            'id_guru' => ['nullable', 'integer', 'exists:guru,id_guru'],
            'id_mapel' => ['required', 'integer', 'exists:mapel,id_mapel'],
            'id_kelas' => ['required', 'integer', 'exists:kelas,id_kelas'],
            'id_jam' => ['required', 'integer', 'exists:jam_pelajaran,id_jam'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
        ]);

        if ($isUpacara) {
            $data['id_guru'] = null;
        }

        $jadwal->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal mengajar berhasil diperbarui.',
        ]);
    }

    /**
     * Menyimpan data jadwal mengajar baru berdasarkan input yang divalidasi.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $mapel = Mapel::findOrFail($request->id_mapel);
        $isUpacara = $mapel->isUpacara();

        $data = $request->validate([
            'id_guru' => ['nullable', 'integer', 'exists:guru,id_guru'],
            'id_mapel' => ['required', 'integer', 'exists:mapel,id_mapel'],
            'id_kelas' => ['required', 'integer', 'exists:kelas,id_kelas'],
            'id_jam' => ['required', 'integer', 'exists:jam_pelajaran,id_jam'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
            'id_tahun_ajaran' => ['required', 'integer', 'exists:tahun_ajaran,id_tahun_ajaran'],
        ]);

        if ($isUpacara) {
            $data['id_guru'] = null;
        }

        $existing = JadwalMengajar::withTrashed()
            ->where('hari', $data['hari'])
            ->where('id_jam', $data['id_jam'])
            ->where('id_kelas', $data['id_kelas'])
            ->where('id_tahun_ajaran', $data['id_tahun_ajaran'])
            ->first();

        if ($existing) {
            $existing->deleted_at = null;
            $existing->update($data);
        } else {
            JadwalMengajar::create($data);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal mengajar berhasil ditambahkan.',
        ]);
    }

    /**
     * Menghapus seluruh jadwal aktif dengan soft delete untuk tahun ajaran aktif.
     *
     * @return JsonResponse
     */
    public function destroyAll()
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $query = JadwalMengajar::whereNull('deleted_at');

        if ($tahunAjaran) {
            $query->where('id_tahun_ajaran', $tahunAjaran->id_tahun_ajaran);
        }

        $deleted = $query->count();
        $query->delete();

        return response()->json([
            'status' => 'success',
            'message' => $deleted.' jadwal berhasil dihapus sementara.',
            'deleted' => $deleted,
        ]);
    }

    /**
     * Menghapus data jadwal mengajar berdasarkan ID.
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        JadwalMengajar::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal mengajar berhasil dihapus.',
        ]);
    }
}

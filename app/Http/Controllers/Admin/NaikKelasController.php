<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Tingkat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Mengelola proses kenaikan kelas siswa: pratinjau, eksekusi promosi, dan data alumni.
 */
class NaikKelasController extends Controller
{
    /**
     * Menampilkan halaman naik kelas beserta ringkasan siswa per kelas dan data alumni tahunan.
     *
     * @return View
     */
    public function index()
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first();

        $allKelas = Kelas::withCount('siswa')
            ->whereNull('deleted_at')
            ->orderBy('tingkat_kelas')
            ->orderBy('jurusan')
            ->orderBy('nama_kelas')
            ->get();

        $siswaPerKelas = Siswa::with('kelas')
            ->where('is_aktif', 1)
            ->whereNull('deleted_at')
            ->get()
            ->groupBy(function ($s) {
                return ($s->kelas->tingkat_kelas ?? '').'|'.($s->kelas->jurusan ?? '');
            });

        $ringkasan = [];
        foreach ($allKelas as $k) {
            $key = $k->tingkat_kelas.'|'.$k->jurusan;
            $count = $k->siswa_count ?? 0;
            if (! isset($ringkasan[$key])) {
                $ringkasan[$key] = [
                    'tingkat' => $k->tingkat_kelas,
                    'jurusan' => $k->jurusan,
                    'kelas' => [],
                    'total' => 0,
                ];
            }
            $ringkasan[$key]['kelas'][] = [
                'id' => $k->id_kelas,
                'nama' => $k->nama_kelas,
                'jml' => $count,
            ];
            $ringkasan[$key]['total'] += $count;
        }

        usort($ringkasan, function ($a, $b) {
            $order = Tingkat::getActiveTingkat()->pluck('urutan', 'nama_tingkat')->toArray();

            return ($order[$a['tingkat']] ?? 0) - ($order[$b['tingkat']] ?? 0);
        });

        $alumniTahunan = Alumni::select('tahun_lulus')
            ->selectRaw('COUNT(*) as jumlah')
            ->groupBy('tahun_lulus')
            ->orderByDesc('tahun_lulus')
            ->get();

        return view('admin.pages.naik-kelas', compact(
            'tahunAjaran', 'allKelas', 'ringkasan', 'alumniTahunan'
        ));
    }

    /**
     * Mengembalikan data pratinjau kenaikan kelas: siswa lulus, promosi XI ke XII, dan promosi X ke XI.
     *
     * @return JsonResponse
     */
    public function preview()
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first();
        if (! $tahunAjaran) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada tahun ajaran aktif.'], 422);
        }

        $allSiswa = Siswa::with('kelas')
            ->where('is_aktif', 1)
            ->whereNull('deleted_at')
            ->get();

        $xii = $allSiswa->filter(fn ($s) => strtoupper($s->kelas->tingkat_kelas ?? '') === 'XII');
        $xi = $allSiswa->filter(fn ($s) => strtoupper($s->kelas->tingkat_kelas ?? '') === 'XI');
        $x = $allSiswa->filter(fn ($s) => strtoupper($s->kelas->tingkat_kelas ?? '') === 'X');

        $promoX = $x->map(function ($s) {
            return [
                'nisn' => $s->nisn,
                'nama' => $s->nama_siswa,
                'kelas_sekarang' => $s->kelas->nama_kelas ?? '-',
                'kelas_baru' => str_replace('X ', 'XI ', $s->kelas->nama_kelas ?? ''),
            ];
        });

        $promoXI = $xi->map(function ($s) {
            return [
                'nisn' => $s->nisn,
                'nama' => $s->nama_siswa,
                'kelas_sekarang' => $s->kelas->nama_kelas ?? '-',
                'kelas_baru' => str_replace('XI ', 'XII ', $s->kelas->nama_kelas ?? ''),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'lulus' => $xii->count(),
                'xi_ke_xii' => $promoXI->values(),
                'x_ke_xi' => $promoX->values(),
            ],
        ]);
    }

    /**
     * Mengeksekusi proses kenaikan kelas: meluluskan siswa XII, mempromosikan ke kelas berikutnya, dan menahan siswa tertentu.
     *
     * @return JsonResponse
     */
    public function execute(Request $request)
    {
        $retainedIds = $request->input('retained_ids', []);

        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first();
        if (! $tahunAjaran) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada tahun ajaran aktif.'], 422);
        }

        $allSiswa = Siswa::with('kelas')
            ->where('is_aktif', 1)
            ->whereNull('deleted_at')
            ->get();

        $xii = $allSiswa->filter(fn ($s) => strtoupper($s->kelas->tingkat_kelas ?? '') === 'XII');
        $xi = $allSiswa->filter(fn ($s) => strtoupper($s->kelas->tingkat_kelas ?? '') === 'XI');
        $x = $allSiswa->filter(fn ($s) => strtoupper($s->kelas->tingkat_kelas ?? '') === 'X');

        if ($xii->isEmpty() && $xi->isEmpty() && $x->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada siswa aktif untuk dipromosikan.'], 422);
        }

        DB::beginTransaction();

        try {
            $snapshot = [];
            foreach ($allSiswa as $s) {
                $tingkat = strtoupper($s->kelas->tingkat_kelas ?? '');
                $jurusan = $s->kelas->jurusan ?? '';
                $nomor = preg_replace('/^\S+\s+\S+\s+/', '', $s->kelas->nama_kelas ?? '');

                if (in_array($s->id_siswa, $retainedIds)) {
                    $snapshot[$s->id_siswa] = ['action' => 'retain'];

                    continue;
                }

                if ($tingkat === 'XII') {
                    $snapshot[$s->id_siswa] = ['action' => 'lulus'];
                } elseif ($tingkat === 'XI') {
                    $snapshot[$s->id_siswa] = [
                        'action' => 'promote',
                        'new_tingkat' => 'XII',
                        'jurusan' => $jurusan,
                        'nomor' => $nomor,
                    ];
                } elseif ($tingkat === 'X') {
                    $snapshot[$s->id_siswa] = [
                        'action' => 'promote',
                        'new_tingkat' => 'XI',
                        'jurusan' => $jurusan,
                        'nomor' => $nomor,
                    ];
                }
            }

            $alumniCount = 0;
            $retainCount = 0;
            foreach ($snapshot as $siswaId => $info) {
                if ($info['action'] === 'lulus') {
                    $siswa = $allSiswa->firstWhere('id_siswa', $siswaId);
                    if (! $siswa) {
                        continue;
                    }

                    Alumni::create([
                        'nisn' => $siswa->nisn,
                        'nama_siswa' => $siswa->nama_siswa,
                        'jenis_kelamin' => $siswa->jenis_kelamin,
                        'nama_kelas' => $siswa->kelas->nama_kelas ?? '',
                        'tingkat_kelas' => $siswa->kelas->tingkat_kelas ?? '',
                        'jurusan' => $siswa->kelas->jurusan ?? '',
                        'tahun_lulus' => $tahunAjaran->tahun_ajaran,
                        'tanggal_lulus' => now()->toDateString(),
                    ]);

                    $siswa->update(['is_aktif' => 0]);
                    $alumniCount++;
                } elseif ($info['action'] === 'retain') {
                    $retainCount++;
                }
            }

            $newClassCache = [];
            $promoCount = 0;

            foreach ($snapshot as $siswaId => $info) {
                if ($info['action'] !== 'promote') {
                    continue;
                }

                $newNamaKelas = $info['new_tingkat'].' '.$info['jurusan'].' '.$info['nomor'];
                $cacheKey = strtolower($newNamaKelas);

                if (! isset($newClassCache[$cacheKey])) {
                    $existing = Kelas::withTrashed()
                        ->where('nama_kelas', $newNamaKelas)
                        ->first();

                    if ($existing) {
                        if ($existing->trashed()) {
                            $existing->restore();
                        }
                        $newClassCache[$cacheKey] = $existing->id_kelas;
                    } else {
                        $newKelas = Kelas::create([
                            'nama_kelas' => $newNamaKelas,
                            'tingkat_kelas' => $info['new_tingkat'],
                            'jurusan' => $info['jurusan'],
                            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                            'id_wali_kelas' => null,
                        ]);
                        $newClassCache[$cacheKey] = $newKelas->id_kelas;
                    }
                }

                $siswa = $allSiswa->firstWhere('id_siswa', $siswaId);
                if ($siswa) {
                    $siswa->update(['id_kelas' => $newClassCache[$cacheKey]]);
                    $promoCount++;
                }
            }

            DB::commit();

            $msg = "Naik kelas selesai! $alumniCount siswa lulus, $promoCount siswa dipromosikan.";
            if ($retainCount > 0) {
                $msg .= " $retainCount siswa tidak naik kelas.";
            }

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'lulus' => $alumniCount,
                'promosi' => $promoCount,
                'retain' => $retainCount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan naik kelas: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengembalikan daftar alumni yang dikelompokkan berdasarkan tahun lulus.
     *
     * @return JsonResponse
     */
    public function alumniList()
    {
        $alumni = Alumni::orderByDesc('tanggal_lulus')
            ->orderBy('nama_siswa')
            ->get()
            ->groupBy('tahun_lulus');

        return response()->json([
            'status' => 'success',
            'data' => $alumni,
        ]);
    }

    /**
     * Mengembalikan daftar siswa aktif berdasarkan ID kelas.
     *
     * @return JsonResponse
     */
    public function getSiswaByKelas($id_kelas)
    {
        $siswa = Siswa::where('id_kelas', $id_kelas)
            ->where('is_aktif', 1)
            ->whereNull('deleted_at')
            ->orderBy('nama_siswa')
            ->select('id_siswa', 'nisn', 'nama_siswa', 'jenis_kelamin')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $siswa,
        ]);
    }
}

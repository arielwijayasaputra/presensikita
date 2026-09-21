<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Guru\AbsensiController;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Mengelola notifikasi untuk admin.
 */
class NotifikasiController extends Controller
{
    /**
     * Mengambil daftar notifikasi terbaru beserta waktu relatifnya.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $guruId = session('auth_guru_id');
        $isAdmin = (session('auth_is_admin') == 1) || (session('auth_role') === 'admin');

        // Hanya tampilkan notifikasi aktif dalam 1 hari terakhir (24 jam)
        $query = DB::table('notifikasi')
            ->whereNull('deleted_at')
            ->where('created_at', '>=', Carbon::now()->subDay());

        if ($isAdmin) {
            // Admin melihat notifikasi yang ditujukan untuk admin (id_guru NULL) atau notifikasi miliknya
            $query->where(function ($q) use ($guruId) {
                $q->whereNull('id_guru');
                if ($guruId) {
                    $q->orWhere('id_guru', $guruId);
                }
            });
        } elseif ($guruId) {
            $isGuru = session('auth_role') === 'guru';
            $activeKelasId = $request->get('kelas_id') ?: ($isGuru ? AbsensiController::getActiveKelasIdForGuru((int) $guruId) : null);

            $query->where(function ($q) use ($guruId, $isGuru, $activeKelasId) {
                $q->where(function ($sub) {
                    $sub->whereNull('id_guru')->whereNull('id_kelas');
                });

                if ($isGuru) {
                    $q->orWhere(function ($sub) use ($guruId, $activeKelasId) {
                        $sub->where('id_guru', $guruId);
                        if ($activeKelasId) {
                            $sub->where(function ($sub2) use ($activeKelasId) {
                                $sub2->whereNull('id_kelas')
                                    ->orWhere('id_kelas', $activeKelasId);
                            });
                        } else {
                            $sub->whereNull('id_kelas');
                        }
                    });
                } else {
                    $q->orWhere('id_guru', $guruId);
                }
            });
        } else {
            // Role lainnya hanya melihat broadcast global
            $query->whereNull('id_guru')->whereNull('id_kelas');
        }

        $rows = $query->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $items = $rows->map(function ($n) {
            return [
                'id' => $n->id,
                'id_kelas' => $n->id_kelas,
                'judul' => $n->judul,
                'pesan' => $n->pesan,
                'tipe' => $n->tipe ?? 'info',  // info | success | warning | error
                'is_read' => (bool) $n->is_read,
                'waktu_relatif' => $this->relativeTime($n->created_at),
                'created_at' => $n->created_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'notifikasi' => $items,
            'total_baru' => $items->where('is_read', false)->count(),
        ]);
    }

    /**
     * Menandai notifikasi tertentu atau seluruhnya sudah dibaca.
     *
     * @return JsonResponse
     */
    public function markRead(Request $request)
    {
        $guruId = session('auth_guru_id');
        $isAdmin = (session('auth_is_admin') == 1) || (session('auth_role') === 'admin');
        $ids = $request->input('ids', []);

        if (! empty($ids)) {
            DB::table('notifikasi')->whereIn('id', $ids)->update(['is_read' => 1]);
        } else {
            if ($isAdmin) {
                DB::table('notifikasi')->whereNull('id_guru')->update(['is_read' => 1]);
                if ($guruId) {
                    DB::table('notifikasi')->where('id_guru', $guruId)->update(['is_read' => 1]);
                }
            } elseif ($guruId) {
                DB::table('notifikasi')->where('id_guru', $guruId)->update(['is_read' => 1]);
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Mengubah timestamp menjadi keterangan waktu relatif dalam Bahasa Indonesia.
     */
    private function relativeTime($timestamp): string
    {
        if (! $timestamp) {
            return '';
        }
        try {
            $dt = Carbon::parse($timestamp);
            $diff = $dt->diffInSeconds(Carbon::now());
            if ($diff < 60) {
                return 'Baru saja';
            }
            if ($diff < 3600) {
                return (int) ($diff / 60).' menit yang lalu';
            }
            if ($diff < 86400) {
                return (int) ($diff / 3600).' jam yang lalu';
            }

            return (int) ($diff / 86400).' hari yang lalu';
        } catch (\Throwable $e) {
            return '';
        }
    }
}

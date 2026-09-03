<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        // Ambil notifikasi dari DB, terbaru dulu, max 20
        $rows = DB::table('notifikasi')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $items = $rows->map(function ($n) {
            return [
                'id' => $n->id,
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
        $ids = $request->input('ids', []);
        if (! empty($ids)) {
            DB::table('notifikasi')->whereIn('id', $ids)->update(['is_read' => 1]);
        } else {
            // Tandai semua sudah dibaca
            DB::table('notifikasi')->update(['is_read' => 1]);
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

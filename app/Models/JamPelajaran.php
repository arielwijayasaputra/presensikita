<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JamPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'jam_pelajaran';

    protected $primaryKey = 'id_jam';

    public $timestamps = false;

    protected $fillable = [
        'jam_ke', 'hari', 'jam_mulai', 'jam_selesai',
    ];

    /**
     * Cache map jam pelajaran untuk efisiensi formatting
     * @var array|null
     */
    protected static ?array $jamPelajaranCache = null;

    /**
     * Mengubah nomor jam / jam_ke menjadi format jam biasa (contoh: 07:00 - 07:40).
     */
    public static function formatJamKe($jamKe, ?string $hari = null): string
    {
        if (empty($jamKe)) {
            return '-';
        }

        $jamKeInt = (int) $jamKe;

        if (static::$jamPelajaranCache === null) {
            static::$jamPelajaranCache = static::whereNull('deleted_at')->get()->all();
        }

        $slots = static::$jamPelajaranCache;

        // Cek pencocokan hari & jam_ke
        $found = null;
        if ($hari) {
            foreach ($slots as $s) {
                if ($s->hari === $hari && (int) $s->jam_ke === $jamKeInt) {
                    $found = $s;
                    break;
                }
            }
            // Jika hari Jumat dan jamKe < 100
            if (! $found && $hari === 'Jumat' && $jamKeInt < 100) {
                foreach ($slots as $s) {
                    if ($s->hari === 'Jumat' && (int) $s->jam_ke === (100 + $jamKeInt)) {
                        $found = $s;
                        break;
                    }
                }
            }
        }

        // Jika belum ditemukan, coba cari tanpa filter hari
        if (! $found) {
            foreach ($slots as $s) {
                if ((int) $s->jam_ke === $jamKeInt) {
                    $found = $s;
                    break;
                }
            }
        }

        if (! $found && $jamKeInt < 100) {
            foreach ($slots as $s) {
                if ((int) $s->jam_ke === (100 + $jamKeInt)) {
                    $found = $s;
                    break;
                }
            }
        }

        if ($found) {
            return substr($found->jam_mulai, 0, 5) . ' - ' . substr($found->jam_selesai, 0, 5);
        }

        return (string) $jamKe;
    }

    public static function clearCache(): void
    {
        static::$jamPelajaranCache = null;
    }
}


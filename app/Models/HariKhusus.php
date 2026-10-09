<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HariKhusus extends Model
{
    use SoftDeletes;

    protected $table = 'hari_khusus';

    protected $primaryKey = 'id_hari_khusus';

    protected $fillable = [
        'tipe',
        'judul',
        'tanggal_mulai',
        'tanggal_selesai',
        'tingkat',
        'jam_pulang',
        'aturan_presensi',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
        'tingkat' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(AkunAdmin::class, 'created_by', 'id_admin');
    }

    public function updater()
    {
        return $this->belongsTo(AkunAdmin::class, 'updated_by', 'id_admin');
    }

    /**
     * Mendapatkan teks label tingkat yang diformat rapi (contoh: "Kelas 10, 11, 12").
     */
    public function getTingkatFormattedAttribute(): string
    {
        $tingkatList = $this->tingkat ?: [];
        if (empty($tingkatList)) {
            return 'Semua Tingkat';
        }

        sort($tingkatList);
        return 'Kelas ' . implode(', ', $tingkatList);
    }

    /**
     * Memeriksa apakah hari khusus berlaku untuk tingkat tertentu.
     */
    public function isBerlakuUntukTingkat(int|string|null $tingkat): bool
    {
        if (empty($tingkat)) {
            return true;
        }

        $tingkatInt = (int) $tingkat;
        $tingkatList = array_map('intval', $this->tingkat ?: []);

        return in_array($tingkatInt, $tingkatList, true);
    }

    /**
     * Memeriksa apakah hari khusus aktif pada tanggal tertentu.
     */
    public function isBerlakuPadaTanggal(string|Carbon $tanggal): bool
    {
        $dateStr = $tanggal instanceof Carbon ? $tanggal->toDateString() : substr((string) $tanggal, 0, 10);
        $startStr = $this->tanggal_mulai ? Carbon::parse($this->tanggal_mulai)->toDateString() : '';
        $endStr = $this->tanggal_selesai ? Carbon::parse($this->tanggal_selesai)->toDateString() : '';

        return $dateStr >= $startStr && $dateStr <= $endStr;
    }

    /**
     * Scope query untuk hari khusus yang berlaku pada tanggal tertentu.
     */
    public function scopeBerlakuPada($query, string|Carbon $tanggal)
    {
        $dateStr = $tanggal instanceof Carbon ? $tanggal->toDateString() : substr((string) $tanggal, 0, 10);

        return $query->whereDate('tanggal_mulai', '<=', $dateStr)
            ->whereDate('tanggal_selesai', '>=', $dateStr);
    }
}

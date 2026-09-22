<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hari extends Model
{
    /** Nama hari Indonesia berdasarkan ISO-8601 numeric (1=Senin..7=Minggu). */
    private const HARI_INDO_BY_ISO = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    protected $table = 'hari';

    protected $primaryKey = 'id_hari';

    public $timestamps = false;

    protected $fillable = [
        'nama_hari',
        'nama_inggris',
        'singkatan',
        'urutan',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /**
     * Get all active days ordered by urutan.
     */
    public static function getActiveDays()
    {
        return static::where('is_aktif', 1)->orderBy('urutan')->get();
    }

    /**
     * Get day name from English full name (e.g. 'Monday' => 'Senin').
     */
    public static function getNamaHariFromEnglish(string $englishName): ?string
    {
        $day = static::where('nama_inggris', $englishName)->first();

        return $day?->nama_hari;
    }

    /**
     * Get day name from English abbreviation (e.g. 'Mon' => 'Senin').
     */
    public static function getNamaHariFromAbbr(string $abbr): ?string
    {
        $map = [
            'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu',
            'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu',
        ];
        $fullName = $map[$abbr] ?? null;

        return $fullName;
    }

    /**
     * Get Indonesian day name from Carbon dayOfWeekIso (1=Mon..7=Sun).
     */
    public static function getNamaHariFromIso(int $dayOfWeekIso): ?string
    {
        $day = static::where('urutan', $dayOfWeekIso)->first();

        return $day?->nama_hari;
    }

    /**
     * Get Indonesian day name from ISO-8601 numeric day (1=Mon..7=Sun)
     * using a fixed hardcoded map (tidak bergantung pada isi tabel).
     */
    public static function getNamaHariFromDayOfWeek(int $dayOfWeekIso): string
    {
        return self::HARI_INDO_BY_ISO[$dayOfWeekIso] ?? 'Senin';
    }

    /**
     * Get all active weekday names (Mon-Sat) for jadwal.
     */
    public static function getWeekdayNames(): array
    {
        return static::where('is_aktif', 1)
            ->where('urutan', '<=', 6)
            ->orderBy('urutan')
            ->pluck('nama_hari')
            ->toArray();
    }
}

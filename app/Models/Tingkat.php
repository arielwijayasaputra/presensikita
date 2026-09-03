<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tingkat extends Model
{
    protected $table = 'tingkat';

    protected $primaryKey = 'id_tingkat';

    public $timestamps = false;

    protected $fillable = [
        'nama_tingkat',
        'angka',
        'urutan',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    /**
     * Get all active tingkat ordered by urutan.
     */
    public static function getActiveTingkat()
    {
        return static::where('is_aktif', 1)->orderBy('urutan')->get();
    }

    /**
     * Get angka from nama_tingkat (e.g. 'X' => 10, 'XI' => 11, 'XII' => 12).
     */
    public static function getAngka(string $namaTingkat): ?int
    {
        $t = static::where('nama_tingkat', strtoupper($namaTingkat))->first();

        return $t?->angka;
    }

    /**
     * Get the next tingkat (for promotion).
     */
    public static function getNextTingkat(string $current): ?string
    {
        $current = static::where('nama_tingkat', strtoupper($current))->first();
        if (! $current) {
            return null;
        }
        $next = static::where('urutan', $current->urutan + 1)->first();

        return $next?->nama_tingkat;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLaporan extends Model
{
    protected $table = 'status_laporan';

    protected $primaryKey = 'id_status';

    public $timestamps = false;

    protected $fillable = [
        'nama_status',
        'slug_status',
        'deskripsi_status',
        'urutan',
    ];

    /**
     * Get all status options as array of slug => nama.
     */
    public static function getOptions(): array
    {
        return static::orderBy('urutan')
            ->pluck('nama_status', 'slug_status')
            ->toArray();
    }

    /**
     * Get all status slugs for validation.
     */
    public static function getSlugs(): array
    {
        return static::pluck('slug_status')->toArray();
    }
}

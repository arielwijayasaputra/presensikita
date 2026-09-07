<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mapel extends Model
{
    use SoftDeletes;

    protected $table = 'mapel';

    protected $primaryKey = 'id_mapel';

    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kelompok',
    ];

    public function jadwal()
    {
        return $this->hasMany(JadwalMengajar::class, 'id_mapel', 'id_mapel');
    }

    /**
     * Memeriksa apakah mapel ini merupakan jadwal upacara/apel.
     */
    public function isUpacara(): bool
    {
        $nama = strtolower((string) ($this->nama_mapel ?? ''));
        $kode = strtolower((string) ($this->kode_mapel ?? ''));

        return str_contains($nama, 'upacara') || str_contains($kode, 'upacara');
    }

    /**
     * Memeriksa apakah string nama mapel merupakan upacara/apel.
     */
    public static function isUpacaraName(?string $name): bool
    {
        if (empty($name)) {
            return false;
        }

        $norm = strtolower(preg_replace('/[^a-z0-9]+/i', '', $name));

        return str_contains($norm, 'upacara');
    }
}

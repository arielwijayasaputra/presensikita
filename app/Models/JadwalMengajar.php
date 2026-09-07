<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalMengajar extends Model
{
    use SoftDeletes;

    protected $table = 'jadwal_mengajar';

    protected $primaryKey = 'id_jadwal';

    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'id_guru',
        'id_mapel',
        'id_kelas',
        'id_jam',
        'hari',
        'id_tahun_ajaran',
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    /**
     * Memeriksa apakah jadwal ini merupakan upacara/apel.
     */
    public function isUpacara(): bool
    {
        if ($this->relationLoaded('mapel') && $this->mapel) {
            return $this->mapel->isUpacara();
        }

        $mapel = $this->mapel ?? Mapel::find($this->id_mapel);

        return $mapel ? $mapel->isUpacara() : false;
    }
}

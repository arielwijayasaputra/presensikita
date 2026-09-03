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
}

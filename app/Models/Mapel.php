<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapel';
    protected $primaryKey = 'id_mapel';
    public $timestamps = false;

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
    ];

    public function jadwal()
    {
        return $this->hasMany(JadwalMengajar::class, 'id_mapel', 'id_mapel');
    }
}

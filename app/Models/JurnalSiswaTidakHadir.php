<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalSiswaTidakHadir extends Model
{
    protected $table = 'jurnal_siswa_tidak_hadir';
    protected $primaryKey = 'id_absen';
    public $timestamps = false;

    protected $fillable = [
        'id_jurnal', 'id_siswa', 'status', 'keterangan'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}

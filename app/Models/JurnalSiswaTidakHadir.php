<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalSiswaTidakHadir extends Model
{
    use SoftDeletes;

    protected $table = 'jurnal_siswa_tidak_hadir';

    protected $primaryKey = 'id_absen';

    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'id_jurnal', 'id_siswa', 'status', 'keterangan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}

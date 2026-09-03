<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalKelas extends Model
{
    use SoftDeletes;

    protected $table = 'jurnal_kelas';

    protected $primaryKey = 'id_jurnal';

    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'id_jadwal', 'id_guru', 'tanggal', 'status_kehadiran_guru', 'materi', 'jumlah_hadir', 'waktu_input',
    ];

    public function siswaTidakHadir()
    {
        return $this->hasMany(JurnalSiswaTidakHadir::class, 'id_jurnal', 'id_jurnal');
    }
}

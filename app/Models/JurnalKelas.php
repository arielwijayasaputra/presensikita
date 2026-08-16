<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalKelas extends Model
{
    protected $table = 'jurnal_kelas';
    protected $primaryKey = 'id_jurnal';
    public $timestamps = false;

    protected $fillable = [
        'id_jadwal', 'id_guru', 'tanggal', 'status_kehadiran_guru', 'materi', 'jumlah_hadir', 'waktu_input'
    ];

    public function siswaTidakHadir()
    {
        return $this->hasMany(JurnalSiswaTidakHadir::class, 'id_jurnal', 'id_jurnal');
    }
}

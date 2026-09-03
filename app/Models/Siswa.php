<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';

    protected $primaryKey = 'id_siswa';

    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'nisn', 'nama_siswa', 'jenis_kelamin', 'id_kelas', 'is_aktif', 'no_hp_ortu',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}

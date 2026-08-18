<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumni extends Model
{
    use SoftDeletes;

    protected $table = 'alumni';
    protected $primaryKey = 'id_alumni';
    public $timestamps = true;

    protected $fillable = [
        'nisn', 'nama_siswa', 'jenis_kelamin',
        'nama_kelas', 'tingkat_kelas', 'jurusan',
        'tahun_lulus', 'tanggal_lulus',
    ];
}

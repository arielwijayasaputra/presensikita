<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'nama_kelas', 'tingkat_kelas', 'jurusan', 'id_tahun_ajaran', 'id_wali_kelas', 'deleted_at'
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'id_wali_kelas', 'id_guru');
    }

    public function getTingkatAngkaAttribute()
    {
        $map = [
            'X'   => '10',
            'XI'  => '11',
            'XII' => '12',
        ];

        return $map[strtoupper($this->tingkat_kelas)] ?? $this->tingkat_kelas;
    }
}

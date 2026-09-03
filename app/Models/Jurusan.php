<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurusan extends Model
{
    use SoftDeletes;

    protected $table = 'jurusan';

    protected $primaryKey = 'id_jurusan';

    protected $fillable = [
        'kode_jurusan',
        'nama_jurusan',
        'deskripsi',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'jurusan', 'kode_jurusan');
    }

    public function countSiswaAktif(): int
    {
        return Siswa::where('siswa.is_aktif', 1)
            ->whereNull('siswa.deleted_at')
            ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->whereNull('kelas.deleted_at')
            ->where('kelas.jurusan', $this->kode_jurusan)
            ->count();
    }

    public function getJumlahSiswaAktifAttribute(): int
    {
        return $this->countSiswaAktif();
    }
}

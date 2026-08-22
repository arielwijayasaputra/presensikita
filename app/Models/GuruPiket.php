<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruPiket extends Model
{
    protected $table = 'guru_piket';
    protected $primaryKey = 'id_guru_piket';

    protected $fillable = ['id_guru', 'tanggal'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }
}

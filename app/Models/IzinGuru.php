<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IzinGuru extends Model
{
    use SoftDeletes;

    protected $table = 'izin_guru';

    protected $primaryKey = 'id_izin_guru';

    protected $fillable = [
        'id_guru',
        'id_guru_piket',
        'tanggal_izin',
        'alasan',
        'foto_surat',
        'status_kepsek',
        'status_waka',
        'catatan_kepsek',
        'catatan_waka',
        'disetujui_kepsek_pada',
        'disetujui_waka_pada',
    ];

    protected $casts = [
        'tanggal_izin' => 'date',
        'disetujui_kepsek_pada' => 'datetime',
        'disetujui_waka_pada' => 'datetime',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function guruPiket()
    {
        return $this->belongsTo(Guru::class, 'id_guru_piket', 'id_guru');
    }

    public function isDisetujui(): bool
    {
        return $this->status_kepsek === 'disetujui' && $this->status_waka === 'disetujui';
    }
}

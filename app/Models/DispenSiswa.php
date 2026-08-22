<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispenSiswa extends Model
{
    protected $table = 'dispen_siswa';
    protected $primaryKey = 'id_dispen_siswa';

    protected $fillable = [
        'id_siswa', 'id_guru_piket', 'tanggal_dispen', 'alasan',
        'status_waka', 'status_guru_piket', 'catatan_waka', 'catatan_guru_piket',
        'disetujui_waka_pada', 'disetujui_guru_piket_pada',
    ];

    protected $casts = [
        'tanggal_dispen' => 'date',
        'disetujui_waka_pada' => 'datetime',
        'disetujui_guru_piket_pada' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function guruPiket()
    {
        return $this->belongsTo(Guru::class, 'id_guru_piket', 'id_guru');
    }

    public function isDisetujui(): bool
    {
        return $this->status_waka === 'disetujui';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class KeterlambatanSiswa extends Model
{
    use SoftDeletes;

    protected $table = 'keterlambatan_siswa';

    protected $primaryKey = 'id_keterlambatan';

    protected $fillable = [
        'id_siswa',
        'id_guru_piket',
        'tanggal',
        'jam_masuk',
        'jam_ke',
        'alasan',
        'foto_surat',
        'status',
        'disetujui_pada',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'disetujui_pada' => 'datetime',
        'jam_ke' => 'integer',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function guruPiket()
    {
        return $this->belongsTo(Guru::class, 'id_guru_piket', 'id_guru');
    }

    public function getFotoSuratUrlAttribute(): ?string
    {
        return $this->foto_surat ? Storage::disk('public')->url($this->foto_surat) : null;
    }
}

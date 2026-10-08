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
        'status_konfirmasi_piket',
        'dikonfirmasi_piket_pada',
        'catatan_piket',
        'status_kepsek',
        'status_waka',
        'catatan_kepsek',
        'catatan_waka',
        'tanda_tangan_kepsek',
        'tanda_tangan_waka',
        'disetujui_kepsek_pada',
        'disetujui_waka_pada',
    ];

    protected $casts = [
        'tanggal_izin' => 'date',
        'dikonfirmasi_piket_pada' => 'datetime',
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

    public function isDikonfirmasiPiket(): bool
    {
        return $this->status_konfirmasi_piket === 'dikonfirmasi';
    }

    public function isDisetujui(): bool
    {
        return $this->isDikonfirmasiPiket() && $this->status_kepsek === 'disetujui' && $this->status_waka === 'disetujui';
    }

    public function scopeDikonfirmasiPiket($query)
    {
        return $query->where('status_konfirmasi_piket', 'dikonfirmasi');
    }

    public function scopeMenungguPiket($query)
    {
        return $query->where('status_konfirmasi_piket', 'menunggu');
    }

    /**
     * Mengambil tanda tangan Kepala Sekolah. Jika belum tersimpan di baris izin namun izin sudah disetujui,
     * otomatis menggunakan foto tanda tangan Kepala Sekolah yang diatur pada pengaturan bot admin.
     */
    public function getTandaTanganKepsekAttribute($value)
    {
        if (! empty($value)) {
            return $value;
        }

        if ($this->status_kepsek === 'disetujui') {
            return Pengaturan::get('ttd_kepsek');
        }

        return null;
    }
}

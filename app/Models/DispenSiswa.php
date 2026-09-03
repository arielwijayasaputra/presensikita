<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class DispenSiswa extends Model
{
    use SoftDeletes;

    protected $table = 'dispen_siswa';

    protected $primaryKey = 'id_dispen_siswa';

    protected $fillable = [
        'id_siswa', 'id_guru_piket', 'tanggal_dispen', 'alasan',
        'jenis_absen', 'foto_surat', 'id_jurnal',
        'status_waka', 'status_guru_piket', 'catatan_waka', 'catatan_guru_piket',
        'disetujui_waka_pada', 'disetujui_guru_piket_pada',
        'waktu_keluar', 'waktu_masuk',
    ];

    protected $casts = [
        'tanggal_dispen' => 'date',
        'disetujui_waka_pada' => 'datetime',
        'disetujui_guru_piket_pada' => 'datetime',
        'waktu_keluar' => 'datetime',
        'waktu_masuk' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function guruPiket()
    {
        return $this->belongsTo(Guru::class, 'id_guru_piket', 'id_guru');
    }

    public function jurnal()
    {
        return $this->belongsTo(JurnalKelas::class, 'id_jurnal', 'id_jurnal');
    }

    public function isDisetujui(): bool
    {
        return $this->status_waka === 'disetujui';
    }

    public function isDispensasi(): bool
    {
        return $this->jenis_absen === 'D';
    }

    public function sudahKembali(): bool
    {
        return ! empty($this->waktu_masuk);
    }

    public function sedangKeluarSekolah(): bool
    {
        return $this->isDispensasi() && ! empty($this->waktu_keluar) && empty($this->waktu_masuk);
    }

    public function sedangDiDalamSekolah(): bool
    {
        return $this->isDispensasi() && empty($this->waktu_keluar) && empty($this->waktu_masuk);
    }

    public function statusLokasiLabel(): string
    {
        if ($this->sudahKembali()) {
            return 'Sudah kembali ke sekolah';
        }
        if ($this->sedangKeluarSekolah()) {
            return 'Sedang di luar sekolah';
        }
        if ($this->sedangDiDalamSekolah()) {
            return 'Dispen di dalam sekolah';
        }

        return 'Dispensasi';
    }

    public function durasiKeluar(): ?string
    {
        if (empty($this->waktu_keluar)) {
            return null;
        }

        $endTime = $this->waktu_masuk ?? now();
        $diffMinutes = (int) $this->waktu_keluar->diffInMinutes($endTime);

        $hours = (int) floor($diffMinutes / 60);
        $minutes = (int) ($diffMinutes % 60);

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} jam {$minutes} menit";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        } else {
            return "{$minutes} menit";
        }
    }

    public function fotoSuratUrl(): ?string
    {
        if (empty($this->foto_surat)) {
            return null;
        }

        return Storage::disk('public')->url($this->foto_surat);
    }
}

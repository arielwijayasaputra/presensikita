<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'role_pelapor',
        'nama_pelapor',
        'judul',
        'isi_laporan',
        'status',
        'catatan_admin',
    ];
}

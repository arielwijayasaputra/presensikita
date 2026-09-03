<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laporan extends Model
{
    use SoftDeletes;

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

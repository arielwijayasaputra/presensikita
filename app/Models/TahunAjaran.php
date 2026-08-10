<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id_tahun_ajaran';
    public $timestamps = false;

    protected $fillable = [
        'tahun_ajaran', 'semester', 'is_aktif'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAjaran extends Model
{
    use SoftDeletes;

    protected $table = 'tahun_ajaran';

    protected $primaryKey = 'id_tahun_ajaran';

    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'tahun_ajaran', 'semester', 'is_aktif',
    ];
}

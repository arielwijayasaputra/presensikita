<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JamPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'jam_pelajaran';

    protected $primaryKey = 'id_jam';

    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'jam_ke', 'hari', 'jam_mulai', 'jam_selesai',
    ];
}

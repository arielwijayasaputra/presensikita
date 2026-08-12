<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use SoftDeletes;

    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'nip', 'nama_guru', 'Peran', 'foto_profil', 'no_hp', 'username', 'password_hash', 'is_admin', 'is_aktif', 'deleted_at'
    ];
}

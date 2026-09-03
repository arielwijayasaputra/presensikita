<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AkunSatpam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'akun_satpam';

    protected $primaryKey = 'id_satpam';

    protected $fillable = [
        'nama',
        'username',
        'password_hash',
        'no_hp',
        'foto_profil',
        'is_aktif',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_aktif' => 'integer',
    ];

    // Compatibility accessors for blade views & controllers that expect guru-like attributes
    public function getNamaGuruAttribute(): string
    {
        return $this->nama ?? 'Satpam';
    }

    public function getPeranAttribute(): string
    {
        return 'Satpam';
    }

    public function getIsAdminAttribute(): bool
    {
        return false;
    }

    public function getNoTlpAttribute(): ?string
    {
        return $this->attributes['no_hp'] ?? null;
    }

    public function setNoTlpAttribute(?string $value): void
    {
        $this->attributes['no_hp'] = $value;
    }

    public function getIdGuruAttribute(): int
    {
        return (int) $this->id_satpam;
    }
}

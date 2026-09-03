<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AkunAdmin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'akun_admin';

    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nama',
        'username',
        'password',
        'password_hash',
        'no_tlp',
        'no_hp',
        'foto_profil',
        'update_pw_at',
        'update_usn_at',
        'is_aktif',
    ];

    protected $hidden = [
        'password',
        'password_hash',
    ];

    protected $casts = [
        'update_pw_at' => 'datetime',
        'update_usn_at' => 'datetime',
        'is_aktif' => 'integer',
    ];

    // Compatibility accessors for blade views & controllers
    public function getNamaGuruAttribute(): string
    {
        return $this->nama ?? 'Administrator';
    }

    public function getNamaAdminAttribute(): string
    {
        return $this->nama ?? 'Administrator';
    }

    public function getIsAdminAttribute(): bool
    {
        return true;
    }

    public function getPeranAttribute(): string
    {
        return 'Administrator';
    }

    public function getIdGuruAttribute(): int
    {
        return (int) $this->id_admin;
    }

    public function getNoHpAttribute(): ?string
    {
        return $this->attributes['no_hp'] ?? $this->attributes['no_tlp'] ?? null;
    }

    public function setNoHpAttribute(?string $value): void
    {
        $this->attributes['no_hp'] = $value;
        $this->attributes['no_tlp'] = $value;
    }

    public function getNoTlpAttribute(): ?string
    {
        return $this->attributes['no_tlp'] ?? $this->attributes['no_hp'] ?? null;
    }

    public function setNoTlpAttribute(?string $value): void
    {
        $this->attributes['no_tlp'] = $value;
        $this->attributes['no_hp'] = $value;
    }

    public function getPasswordHashAttribute(): ?string
    {
        return $this->attributes['password_hash'] ?? $this->attributes['password'] ?? null;
    }

    public function setPasswordHashAttribute(?string $value): void
    {
        $this->attributes['password_hash'] = $value;
        $this->attributes['password'] = $value;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->attributes['password'] ?? $this->attributes['password_hash'] ?? null;
    }

    public function setPasswordAttribute(?string $value): void
    {
        $this->attributes['password'] = $value;
        $this->attributes['password_hash'] = $value;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'id_role';

    protected $fillable = [
        'nama_role',
        'slug_role',
        'deskripsi',
        'route_name',
        'is_struktural',
    ];

    protected function casts(): array
    {
        return [
            'is_struktural' => 'boolean',
        ];
    }

    /**
     * Get all struktural roles.
     */
    public static function getStrukturalRoles()
    {
        return static::where('is_struktural', 1)->get();
    }

    /**
     * Get all struktural role slugs.
     */
    public static function getStrukturalSlugs(): array
    {
        return static::where('is_struktural', 1)->pluck('slug_role')->toArray();
    }

    /**
     * Get route name from slug.
     */
    public static function getRouteFromSlug(string $slug): ?string
    {
        $role = static::where('slug_role', $slug)->first();

        return $role?->route_name;
    }
}

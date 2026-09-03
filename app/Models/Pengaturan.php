<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $primaryKey = 'kunci';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['kunci', 'nilai'];

    public static function get($key, $default = null)
    {
        $setting = static::find($key);

        return $setting ? $setting->nilai : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['kunci' => $key],
            ['nilai' => $value]
        );
    }
}

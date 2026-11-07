<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    use BelongsToTenant, HasFactory;

    protected $guarded = ['id'];

    public static function get(string $key, mixed $default = null, string $group = 'general'): mixed
    {
        $setting = static::where('group', $group)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value]
        );
    }
}

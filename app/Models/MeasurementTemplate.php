<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeasurementTemplate extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(MeasurementProfile::class, 'template_id');
    }

    public function garmentTypes(): HasMany
    {
        return $this->hasMany(GarmentType::class);
    }
}

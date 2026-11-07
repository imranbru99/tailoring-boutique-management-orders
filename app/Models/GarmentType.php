<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class GarmentType extends Model
{
    use BelongsToTenant, HasFactory, HasUniversalSlug, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'estimated_days' => 'integer',
            'customization_options' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function measurementTemplate(): BelongsTo
    {
        return $this->belongsTo(MeasurementTemplate::class, 'measurement_template_id');
    }

    public function designs(): HasMany
    {
        return $this->hasMany(DesignCatalog::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

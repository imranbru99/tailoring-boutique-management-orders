<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class DesignCatalog extends Model
{
    use BelongsToTenant, HasFactory, HasUniversalSlug, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'additional_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function garmentType(): BelongsTo
    {
        return $this->belongsTo(GarmentType::class);
    }
}

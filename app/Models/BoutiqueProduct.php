<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class BoutiqueProduct extends Model
{
    use BelongsToTenant, HasFactory, HasUniversalSlug, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'retail_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'reorder_point' => 'integer',
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
}

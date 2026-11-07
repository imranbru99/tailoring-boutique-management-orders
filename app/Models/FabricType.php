<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FabricType extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'cost_per_unit' => 'decimal:2',
            'selling_price_per_unit' => 'decimal:2',
            'current_stock' => 'decimal:2',
            'reorder_level' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function rolls(): HasMany
    {
        return $this->hasMany(FabricRoll::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->reorder_level;
    }
}

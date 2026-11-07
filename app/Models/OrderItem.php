<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'material_cost' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'total_price' => 'decimal:2',
            'measurement_snapshot' => 'array',
            'customization_details' => 'array',
            'quantity' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function garmentType(): BelongsTo
    {
        return $this->belongsTo(GarmentType::class);
    }

    public function designCatalog(): BelongsTo
    {
        return $this->belongsTo(DesignCatalog::class);
    }

    public function fabricType(): BelongsTo
    {
        return $this->belongsTo(FabricType::class);
    }

    public function measurementProfile(): BelongsTo
    {
        return $this->belongsTo(MeasurementProfile::class);
    }

    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class);
    }
}

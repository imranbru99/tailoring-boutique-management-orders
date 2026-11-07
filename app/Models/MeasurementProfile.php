<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeasurementProfile extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'values' => 'array',
            'version' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(MeasurementTemplate::class, 'template_id');
    }

    public function measuredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'measured_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(MeasurementVersion::class);
    }

    /**
     * Create an immutable snapshot for an order item.
     * Preserves exact values, unit, and template name at the exact time of order placement.
     */
    public function createSnapshot(): array
    {
        return [
            'profile_id' => $this->id,
            'profile_name' => $this->profile_name,
            'template_id' => $this->template_id,
            'template_name' => $this->template?->name ?? 'Custom',
            'unit' => $this->unit,
            'version' => $this->version,
            'values' => $this->values ?? [],
            'snapshotted_at' => now()->toIso8601String(),
        ];
    }
}

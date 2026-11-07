<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use BelongsToTenant, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_spend' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'date',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function isValid(?float $amount = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($amount !== null && $amount < $this->min_spend) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (!$this->isValid($amount)) {
            return 0.0;
        }

        $discount = match ($this->discount_type) {
            'percentage' => round(($amount * ($this->discount_value / 100)), 2),
            default => min($amount, (float) $this->discount_value),
        };

        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = (float) $this->max_discount;
        }

        return $discount;
    }
}

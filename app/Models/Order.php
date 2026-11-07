<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_delivery_date' => 'date',
            'actual_delivery_date' => 'date',
            'total_material_cost' => 'decimal:2',
            'total_labor_cost' => 'decimal:2',
            'additional_charges' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
            'attachments' => 'array',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedTailor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_tailor_id');
    }

    public function assignedDesigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_designer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class);
    }

    public function qualityInspections(): HasMany
    {
        return $this->hasMany(QualityInspection::class);
    }

    public function fittings(): HasMany
    {
        return $this->hasMany(Fitting::class);
    }

    public function alterations(): HasMany
    {
        return $this->hasMany(Alteration::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class)->latestOfMany();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Recalculate totals from items and discounts/taxes
     */
    public function recalculateTotals(): void
    {
        $materialCost = $this->items()->sum('material_cost');
        $laborCost = $this->items()->sum('labor_cost');
        $itemsTotal = $this->items()->sum('total_price');

        $subtotal = $itemsTotal > 0 ? $itemsTotal : ($materialCost + $laborCost);
        $totalBeforeTax = max(0, $subtotal + $this->additional_charges - $this->discount_amount);
        $taxAmount = round(($totalBeforeTax * ($this->tax_rate / 100)), 2);
        $totalAmount = round($totalBeforeTax + $taxAmount, 2);

        $paidAmount = $this->payments()->where('is_refund', false)->sum('amount') 
                    - $this->payments()->where('is_refund', true)->sum('amount');
        
        $balance = max(0, $totalAmount - $paidAmount);

        $paymentStatus = match (true) {
            $paidAmount <= 0 => 'unpaid',
            $balance <= 0 => 'paid',
            default => 'partial',
        };

        $this->updateQuietly([
            'total_material_cost' => $materialCost,
            'total_labor_cost' => $laborCost,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance_amount' => $balance,
            'payment_status' => $paymentStatus,
        ]);
    }
}

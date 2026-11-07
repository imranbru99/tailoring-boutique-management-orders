<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alteration extends Model
{
    use BelongsToTenant, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'additional_charge' => 'decimal:2',
            'expected_completion_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function fitting(): BelongsTo
    {
        return $this->belongsTo(Fitting::class);
    }

    public function assignedTailor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_tailor_id');
    }
}

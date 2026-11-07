<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricRoll extends Model
{
    use BelongsToTenant, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'initial_quantity' => 'decimal:2',
            'current_quantity' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    public function fabricType(): BelongsTo
    {
        return $this->belongsTo(FabricType::class);
    }
}

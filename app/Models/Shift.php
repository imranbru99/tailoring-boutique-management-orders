<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use BelongsToTenant, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'late_threshold_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EmployeeAttendance::class);
    }
}

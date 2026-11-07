<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasTenants, HasAvatar, HasName
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hourly_rate' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'skills' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => in_array($this->role, ['super_admin', 'platform_admin'], true),
            'app' => !in_array($this->role, ['customer'], true),
            'customer' => $this->role === 'customer' || $this->isBusinessOwner() || $this->isSuperAdmin(),
            default => true,
        };
    }

    public function getTenants(Panel $panel): Collection
    {
        if ($this->isSuperAdmin()) {
            return Tenant::where('is_active', true)->get();
        }

        return $this->tenant ? collect([$this->tenant]) : collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->tenant_id === $tenant->id;
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'platform_admin'], true);
    }

    public function isBusinessOwner(): bool
    {
        return in_array($this->role, ['business_owner', 'super_admin'], true);
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['general_manager', 'branch_manager', 'business_owner', 'super_admin'], true);
    }

    public function isTailor(): bool
    {
        return in_array($this->role, ['master_tailor', 'tailor', 'cutter', 'pattern_maker', 'embroidery_specialist', 'finishing_worker'], true);
    }

    public function isStaff(): bool
    {
        return !in_array($this->role, ['customer', 'super_admin'], true);
    }

    public function hasRole(string|array $roles): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $roles = (array) $roles;
        return in_array($this->role, $roles, true);
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function currentBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_user')->withPivot('is_primary')->withTimestamps();
    }

    public function customer(): HasMany
    {
        return $this->hasMany(Customer::class, 'user_id');
    }

    public function assignedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'assigned_tailor_id');
    }

    public function assignedJobCards(): HasMany
    {
        return $this->hasMany(JobCard::class, 'assigned_to');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EmployeeAttendance::class, 'user_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'user_id');
    }
}

<?php

namespace App\Core\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = static::resolveTenantId();

        if ($tenantId !== null) {
            $builder->where($model->qualifyColumn('tenant_id'), $tenantId);
        }
    }

    public static function resolveTenantId(): ?int
    {
        if (app()->runningInConsole() && !app()->has('current_tenant_id')) {
            // When running queue jobs or console commands without an explicit tenant bound, allow unconstrained if needed,
            // or return the bound id.
            return app()->has('current_tenant_id') ? app('current_tenant_id') : null;
        }

        if (app()->has('current_tenant_id')) {
            return app('current_tenant_id');
        }

        if (auth()->check()) {
            $user = auth()->user();
            // Platform super admin can view all if explicitly desired, otherwise scoped to their active business
            if ($user->role === 'super_admin' && session('bypass_tenant_scope')) {
                return null;
            }
            return $user->tenant_id;
        }

        return session('current_tenant_id');
    }
}

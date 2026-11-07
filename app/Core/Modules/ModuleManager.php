<?php

namespace App\Core\Modules;

use App\Models\Tenant;
use App\Models\TenantModule;
use Illuminate\Support\Facades\Cache;

class ModuleManager
{
    /**
     * Check if a module is enabled for a specific tenant (or current context).
     */
    public function isEnabled(string $moduleKey, ?int $tenantId = null): bool
    {
        $meta = ModuleRegistry::get($moduleKey);
        if (!$meta) {
            return false;
        }

        // Core modules are always enabled
        if ($meta['is_core']) {
            return true;
        }

        $tenantId = $tenantId ?? $this->resolveCurrentTenantId();
        if (!$tenantId) {
            return $meta['default_enabled'] ?? false;
        }

        return Cache::remember("tenant_{$tenantId}_module_{$moduleKey}", 300, function () use ($tenantId, $moduleKey, $meta) {
            $record = TenantModule::where('tenant_id', $tenantId)
                ->where('module_key', $moduleKey)
                ->first();

            if (!$record) {
                return $meta['default_enabled'] ?? false;
            }

            return (bool) $record->is_enabled;
        });
    }

    /**
     * Enable a module for a tenant with dependency validation.
     *
     * @return array{success: bool, error: ?string}
     */
    public function enable(string $moduleKey, ?int $tenantId = null, array $settings = []): array
    {
        $meta = ModuleRegistry::get($moduleKey);
        if (!$meta) {
            return ['success' => false, 'error' => "Module [{$moduleKey}] does not exist in registry."];
        }

        $tenantId = $tenantId ?? $this->resolveCurrentTenantId();
        if (!$tenantId) {
            return ['success' => false, 'error' => 'No active business/tenant context specified.'];
        }

        // Validate dependencies
        $depCheck = $this->checkDependencies($moduleKey, $tenantId);
        if (!$depCheck['can_enable']) {
            $missingNames = array_map(function ($k) {
                return ModuleRegistry::get($k)['name'] ?? $k;
            }, $depCheck['missing']);

            return [
                'success' => false,
                'error' => "Cannot enable [{$meta['name']}]. Required dependency modules are missing or disabled: " . implode(', ', $missingNames) . '.',
            ];
        }

        TenantModule::updateOrCreate(
            ['tenant_id' => $tenantId, 'module_key' => $moduleKey],
            [
                'is_enabled' => true,
                'settings' => $settings,
                'enabled_at' => now(),
                'disabled_at' => null,
                'updated_by' => auth()->id(),
            ]
        );

        Cache::forget("tenant_{$tenantId}_module_{$moduleKey}");
        Cache::forget("tenant_{$tenantId}_enabled_modules");

        return ['success' => true, 'error' => null];
    }

    /**
     * Disable a module for a tenant with dependency protection and core module enforcement.
     *
     * @return array{success: bool, error: ?string}
     */
    public function disable(string $moduleKey, ?int $tenantId = null): array
    {
        $meta = ModuleRegistry::get($moduleKey);
        if (!$meta) {
            return ['success' => false, 'error' => "Module [{$moduleKey}] does not exist."];
        }

        if ($meta['is_core']) {
            return [
                'success' => false,
                'error' => "Module [{$meta['name']}] is an essential Core Module and cannot be deactivated.",
            ];
        }

        $tenantId = $tenantId ?? $this->resolveCurrentTenantId();
        if (!$tenantId) {
            return ['success' => false, 'error' => 'No active tenant context.'];
        }

        // Check if any other ACTIVE module currently depends on this module
        $dependentModules = $this->getActiveDependentModules($moduleKey, $tenantId);
        if (!empty($dependentModules)) {
            $depNames = array_map(fn($k) => ModuleRegistry::get($k)['name'] ?? $k, $dependentModules);
            return [
                'success' => false,
                'error' => "Cannot disable [{$meta['name']}]. It is currently required by active module(s): " . implode(', ', $depNames) . ". Disable them first.",
            ];
        }

        TenantModule::updateOrCreate(
            ['tenant_id' => $tenantId, 'module_key' => $moduleKey],
            [
                'is_enabled' => false,
                'disabled_at' => now(),
                'updated_by' => auth()->id(),
            ]
        );

        Cache::forget("tenant_{$tenantId}_module_{$moduleKey}");
        Cache::forget("tenant_{$tenantId}_enabled_modules");

        return ['success' => true, 'error' => null];
    }

    /**
     * Check if all required dependencies for a module are active.
     *
     * @return array{can_enable: bool, missing: array<string>}
     */
    public function checkDependencies(string $moduleKey, int $tenantId): array
    {
        $meta = ModuleRegistry::get($moduleKey);
        if (!$meta || empty($meta['dependencies'])) {
            return ['can_enable' => true, 'missing' => []];
        }

        $missing = [];
        foreach ($meta['dependencies'] as $requiredKey) {
            if (!$this->isEnabled($requiredKey, $tenantId)) {
                $missing[] = $requiredKey;
            }
        }

        return [
            'can_enable' => empty($missing),
            'missing' => $missing,
        ];
    }

    /**
     * Find active modules that depend on the given module.
     *
     * @return array<string>
     */
    public function getActiveDependentModules(string $moduleKey, int $tenantId): array
    {
        $dependents = [];
        foreach (ModuleRegistry::all() as $key => $mod) {
            if ($key === $moduleKey) {
                continue;
            }
            if (in_array($moduleKey, $mod['dependencies'] ?? [], true)) {
                if ($this->isEnabled($key, $tenantId)) {
                    $dependents[] = $key;
                }
            }
        }

        return $dependents;
    }

    /**
     * Get all enabled module keys for a tenant.
     *
     * @return array<string>
     */
    public function getEnabledModules(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? $this->resolveCurrentTenantId();
        if (!$tenantId) {
            return array_keys(array_filter(ModuleRegistry::all(), fn($m) => $m['is_core'] || $m['default_enabled']));
        }

        return Cache::remember("tenant_{$tenantId}_enabled_modules", 300, function () use ($tenantId) {
            $enabled = [];
            foreach (ModuleRegistry::all() as $key => $meta) {
                if ($this->isEnabled($key, $tenantId)) {
                    $enabled[] = $key;
                }
            }
            return $enabled;
        });
    }

    /**
     * Initialize standard modules for a newly onboarded tenant based on business archetype.
     */
    public function syncDefaultModules(int $tenantId, string $businessType = 'boutique'): void
    {
        $recommended = match ($businessType) {
            'tailoring_shop' => [
                'dashboard', 'customers', 'measurements', 'orders', 'production',
                'employees', 'fabrics', 'billing', 'deliveries', 'alterations', 'settings'
            ],
            'boutique' => [
                'dashboard', 'customers', 'designs', 'fabrics', 'orders', 'pos',
                'inventory', 'suppliers', 'billing', 'accounting', 'marketing',
                'membership', 'reports', 'ai_assistant', 'settings'
            ],
            'fashion_studio' => [
                'dashboard', 'customers', 'measurements', 'designs', 'fabrics',
                'orders', 'production', 'appointments', 'quality_control',
                'alterations', 'billing', 'accounting', 'reports', 'ai_assistant', 'settings'
            ],
            default => array_keys(ModuleRegistry::all()),
        };

        foreach (ModuleRegistry::all() as $key => $meta) {
            $shouldEnable = $meta['is_core'] || in_array($key, $recommended, true);
            TenantModule::updateOrCreate(
                ['tenant_id' => $tenantId, 'module_key' => $key],
                [
                    'is_enabled' => $shouldEnable,
                    'enabled_at' => $shouldEnable ? now() : null,
                    'disabled_at' => $shouldEnable ? null : now(),
                ]
            );
        }

        Cache::forget("tenant_{$tenantId}_enabled_modules");
    }

    /**
     * Synchronize the global module catalog into the database.
     */
    public function syncCatalogToDatabase(): void
    {
        foreach (ModuleRegistry::all() as $key => $meta) {
            \Illuminate\Support\Facades\DB::table('modules')->updateOrInsert(
                ['key' => $key],
                [
                    'name' => $meta['name'],
                    'description' => $meta['description'] ?? null,
                    'category' => $meta['category'] ?? 'operations',
                    'version' => $meta['version'] ?? '1.0.0',
                    'icon' => $meta['icon'] ?? null,
                    'is_core' => $meta['is_core'] ?? false,
                    'dependencies' => json_encode($meta['dependencies'] ?? []),
                    'optional_dependencies' => json_encode($meta['optional_dependencies'] ?? []),
                    'permissions' => json_encode($meta['permissions'] ?? []),
                    'min_plan' => $meta['min_plan'] ?? 'free',
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    /**
     * Resolve the current tenant ID from auth user or session.
     */
    public function resolveCurrentTenantId(): ?int
    {
        if (auth()->check()) {
            return auth()->user()->tenant_id;
        }

        return session('current_tenant_id');
    }
}

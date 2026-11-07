<?php

namespace App\Filament\App\Pages;

use App\Core\Modules\ModuleManager;
use App\Core\Modules\ModuleRegistry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class ModuleManagerPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Module Engine';
    protected static ?string $title = 'Business Module Management Engine';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.app.pages.module-manager-page';

    public string $selectedCategory = 'all';
    public ?string $activeModalModule = null;

    public function getCategories(): array
    {
        return [
            'all' => 'All Modules',
            'core' => 'Core Essentials',
            'operations' => 'Tailoring & Production',
            'sales' => 'Sales & POS',
            'finance' => 'Billing & Finance',
            'hr' => 'Human Resources & Staff',
            'marketing' => 'Marketing & Loyalty',
            'ai' => 'AI Intelligence',
            'system' => 'System & Integration',
        ];
    }

    public function getModulesProperty(): array
    {
        $manager = app(ModuleManager::class);
        $tenantId = auth()->user()?->tenant_id;
        $modules = [];

        foreach (ModuleRegistry::all() as $key => $meta) {
            if ($this->selectedCategory !== 'all' && $meta['category'] !== $this->selectedCategory) {
                continue;
            }

            $isEnabled = $manager->isEnabled($key, $tenantId);
            $depCheck = $manager->checkDependencies($key, $tenantId ?? 0);
            $dependents = $manager->getActiveDependentModules($key, $tenantId ?? 0);
            $recordCount = $this->getRecordCountForModule($key, $tenantId);

            $modules[] = array_merge($meta, [
                'is_enabled' => $isEnabled,
                'can_enable' => $depCheck['can_enable'],
                'missing_dependencies' => $depCheck['missing'],
                'active_dependents' => $dependents,
                'record_count' => $recordCount,
            ]);
        }

        return $modules;
    }

    public function toggleModule(string $moduleKey): void
    {
        $manager = app(ModuleManager::class);
        $tenantId = auth()->user()?->tenant_id;
        $isEnabled = $manager->isEnabled($moduleKey, $tenantId);

        if ($isEnabled) {
            $result = $manager->disable($moduleKey, $tenantId);
            if (!$result['success']) {
                Notification::make()
                    ->title('Cannot Deactivate Module')
                    ->body($result['error'])
                    ->danger()
                    ->persistent()
                    ->send();
                return;
            }

            Notification::make()
                ->title('Module Deactivated')
                ->body("The module [{$moduleKey}] has been deactivated. Business data remains completely preserved.")
                ->warning()
                ->send();
        } else {
            $result = $manager->enable($moduleKey, $tenantId);
            if (!$result['success']) {
                Notification::make()
                    ->title('Activation Blocked')
                    ->body($result['error'])
                    ->danger()
                    ->persistent()
                    ->send();
                return;
            }

            Notification::make()
                ->title('Module Activated')
                ->body("The module [{$moduleKey}] has been enabled and its features are now accessible.")
                ->success()
                ->send();
        }
    }

    protected function getRecordCountForModule(string $moduleKey, ?int $tenantId): int
    {
        if (!$tenantId) {
            return 0;
        }

        return match ($moduleKey) {
            'customers' => DB::table('customers')->where('tenant_id', $tenantId)->count(),
            'measurements' => DB::table('measurement_profiles')->where('tenant_id', $tenantId)->count(),
            'designs' => DB::table('design_catalogs')->where('tenant_id', $tenantId)->count(),
            'fabrics' => DB::table('fabric_types')->where('tenant_id', $tenantId)->count(),
            'orders' => DB::table('orders')->where('tenant_id', $tenantId)->count(),
            'production' => DB::table('job_cards')->where('tenant_id', $tenantId)->count(),
            'employees' => DB::table('users')->where('tenant_id', $tenantId)->where('role', '!=', 'customer')->count(),
            'appointments' => DB::table('appointments')->where('tenant_id', $tenantId)->count(),
            'quality_control' => DB::table('quality_inspections')->where('tenant_id', $tenantId)->count(),
            'alterations' => DB::table('alterations')->where('tenant_id', $tenantId)->count(),
            'deliveries' => DB::table('deliveries')->where('tenant_id', $tenantId)->count(),
            'inventory' => DB::table('inventory_items')->where('tenant_id', $tenantId)->count(),
            'suppliers' => DB::table('suppliers')->where('tenant_id', $tenantId)->count(),
            'billing' => DB::table('invoices')->where('tenant_id', $tenantId)->count(),
            'accounting' => DB::table('expenses')->where('tenant_id', $tenantId)->count(),
            'membership' => DB::table('customer_memberships')->where('tenant_id', $tenantId)->count(),
            'attendance' => DB::table('employee_attendances')->where('tenant_id', $tenantId)->count(),
            default => 0,
        };
    }
}

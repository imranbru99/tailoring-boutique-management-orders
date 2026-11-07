<?php

namespace Tests\Feature;

use App\Core\Modules\ModuleManager;
use App\Core\Modules\ModuleRegistry;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_thirty_modules_are_registered_with_metadata(): void
    {
        $modules = ModuleRegistry::all();
        $this->assertCount(30, $modules);

        // Core required modules exist
        $this->assertTrue(ModuleRegistry::has('dashboard'));
        $this->assertTrue(ModuleRegistry::has('customers'));
        $this->assertTrue(ModuleRegistry::has('measurements'));
        $this->assertTrue(ModuleRegistry::has('orders'));
        $this->assertTrue(ModuleRegistry::has('production'));
        $this->assertTrue(ModuleRegistry::has('pos'));
        $this->assertTrue(ModuleRegistry::has('inventory'));
        $this->assertTrue(ModuleRegistry::has('ai_assistant'));
        $this->assertTrue(ModuleRegistry::has('attendance'));
    }

    public function test_core_modules_cannot_be_disabled(): void
    {
        $manager = app(ModuleManager::class);
        $manager->syncCatalogToDatabase();

        $tenant = Tenant::create([
            'name' => 'Elite Tailors',
            'slug' => 'elite-tailors',
            'is_active' => true,
        ]);

        // Attempting to disable 'customers' (a core module)
        $result = $manager->disable('customers', $tenant->id);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('essential Core Module and cannot be deactivated', $result['error']);
    }

    public function test_disabling_dependency_module_is_rejected_when_dependents_are_active(): void
    {
        $manager = app(ModuleManager::class);
        $manager->syncCatalogToDatabase();

        $tenant = Tenant::create([
            'name' => 'Boutique Atelier',
            'slug' => 'boutique-atelier',
            'is_active' => true,
        ]);

        // Enable employees and attendance
        $manager->enable('employees', $tenant->id);
        $manager->enable('attendance', $tenant->id);

        $this->assertTrue($manager->isEnabled('attendance', $tenant->id));

        // Attempt to disable 'employees' while 'attendance' depends on it
        $result = $manager->disable('employees', $tenant->id);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('required by active module', $result['error']);
    }

    public function test_module_can_be_enabled_and_disabled_cleanly(): void
    {
        $manager = app(ModuleManager::class);
        $manager->syncCatalogToDatabase();

        $tenant = Tenant::create([
            'name' => 'Studio Paris',
            'slug' => 'studio-paris',
            'is_active' => true,
        ]);

        // Enable marketing
        $enableResult = $manager->enable('marketing', $tenant->id);
        $this->assertTrue($enableResult['success']);
        $this->assertTrue($manager->isEnabled('marketing', $tenant->id));

        // Disable marketing
        $disableResult = $manager->disable('marketing', $tenant->id);
        $this->assertTrue($disableResult['success']);
        $this->assertFalse($manager->isEnabled('marketing', $tenant->id));
    }
}

<?php

namespace Tests\Feature;

use App\Core\Modules\ModuleManager;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PublicMarketingWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(ModuleManager::class)->syncCatalogToDatabase();
    }

    public function test_public_pages_load_successfully(): void
    {
        $tenant = Tenant::create([
            'name' => 'Demo House',
            'slug' => 'demo-house',
            'is_active' => true,
        ]);

        $routes = [
            '/',
            '/about',
            '/modules',
            '/modules/customers',
            '/solutions/tailoring',
            '/solutions/boutique',
            '/pricing',
            '/demo',
            '/docs',
            '/contact',
            '/faq',
            '/terms',
            '/privacy',
            '/onboarding',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200, "Failed asserting route {$route} returns 200");
        }
    }

    public function test_demo_switcher_authenticates_demo_persona(): void
    {
        $tenant = Tenant::create([
            'name' => 'Demo House',
            'slug' => 'demo-house',
            'is_active' => true,
        ]);

        $owner = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Demo Owner',
            'email' => 'owner@tailor.test',
            'password' => Hash::make('Demo@12345!'),
            'role' => 'business_owner',
            'is_active' => true,
        ]);

        $response = $this->post('/demo/login', [
            'email' => 'owner@tailor.test',
        ]);

        $response->assertRedirect("/app/{$tenant->slug}");
        $this->assertAuthenticatedAs($owner);
    }
}

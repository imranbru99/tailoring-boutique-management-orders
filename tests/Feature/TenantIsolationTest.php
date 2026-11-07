<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_belonging_to_one_tenant_cannot_be_accessed_by_another_tenant(): void
    {
        // 1. Create Tenant A
        $tenantA = Tenant::create([
            'name' => 'Savile Row Atelier',
            'slug' => 'savile-row',
            'business_type' => 'tailoring_shop',
            'currency' => 'GBP',
            'currency_symbol' => '£',
            'measurement_unit' => 'inch',
            'timezone' => 'Europe/London',
            'is_active' => true,
        ]);

        $userA = User::create([
            'tenant_id' => $tenantA->id,
            'name' => 'Lord Tailor A',
            'email' => 'tailor_a@savilerow.test',
            'password' => Hash::make('password123'),
            'role' => 'business_owner',
            'is_active' => true,
        ]);

        $customerA = Customer::create([
            'tenant_id' => $tenantA->id,
            'customer_code' => 'CUST-A-001',
            'first_name' => 'Edward',
            'last_name' => 'Oxford',
            'phone' => '+44 7700 900077',
            'gender' => 'male',
        ]);

        // 2. Create Tenant B
        $tenantB = Tenant::create([
            'name' => 'Milano Alta Moda',
            'slug' => 'milano-moda',
            'business_type' => 'boutique',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'measurement_unit' => 'cm',
            'timezone' => 'Europe/Rome',
            'is_active' => true,
        ]);

        $userB = User::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Signor Tailor B',
            'email' => 'tailor_b@milanomoda.test',
            'password' => Hash::make('password123'),
            'role' => 'business_owner',
            'is_active' => true,
        ]);

        $customerB = Customer::create([
            'tenant_id' => $tenantB->id,
            'customer_code' => 'CUST-B-001',
            'first_name' => 'Matteo',
            'last_name' => 'Ferrari',
            'phone' => '+39 02 1234567',
            'gender' => 'male',
        ]);

        // 3. Act as User B and access customer API
        $this->actingAs($userB);

        $response = $this->getJson('/api/v1/customers');
        $response->assertStatus(200);

        // Assert customer B is present, but customer A is strictly invisible
        $response->assertJsonFragment(['customer_code' => 'CUST-B-001']);
        $response->assertJsonMissing(['customer_code' => 'CUST-A-001']);

        // Assert direct show lookup for Customer A fails with 404 for User B
        $showResponse = $this->getJson("/api/v1/customers/{$customerA->id}");
        $showResponse->assertStatus(404);
    }
}

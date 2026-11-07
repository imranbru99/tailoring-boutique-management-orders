<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_authentication_issues_bearer_token(): void
    {
        $tenant = Tenant::create([
            'name' => 'Bond Street Atelier',
            'slug' => 'bond-street',
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Henry Higgins',
            'email' => 'henry@bondstreet.test',
            'password' => Hash::make('SecretPass123!'),
            'role' => 'business_owner',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'henry@bondstreet.test',
            'password' => 'SecretPass123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email', 'role', 'tenant_id'],
            ]);
    }

    public function test_authenticated_user_can_create_and_fetch_customers(): void
    {
        $tenant = Tenant::create([
            'name' => 'Savile Tailoring',
            'slug' => 'savile-tailoring',
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Master Tailor Arthur',
            'email' => 'arthur@savile.test',
            'password' => Hash::make('SecretPass123!'),
            'role' => 'tailor',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'sanctum');

        // Create customer via API
        $createResponse = $this->postJson('/api/v1/customers', [
            'first_name' => 'Galahad',
            'last_name' => 'Kingsman',
            'phone' => '+44 7911 123456',
            'gender' => 'male',
            'notes' => 'Oxfords not brogues.',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('customer.first_name', 'Galahad')
            ->assertJsonPath('customer.tenant_id', $tenant->id);

        $customerId = $createResponse->json('customer.id');

        // Show customer
        $showResponse = $this->getJson("/api/v1/customers/{$customerId}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('customer.last_name', 'Kingsman');
    }

    public function test_biometric_attendance_punch_records_check_in(): void
    {
        $tenant = Tenant::create([
            'name' => 'Atelier Workshop',
            'slug' => 'atelier-workshop',
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Stitcher Karim',
            'email' => 'karim@atelier.test',
            'password' => Hash::make('SecretPass123!'),
            'role' => 'tailor',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'sanctum');

        $punchResponse = $this->postJson('/api/v1/attendance/punch', [
            'type' => 'check_in',
            'device_uid' => 'ZK-BIO-TERMINAL-01',
            'verification_mode' => 'fingerprint',
        ]);

        $punchResponse->assertStatus(200)
            ->assertJsonPath('message', 'Successfully recorded check_in.');

        $this->assertDatabaseHas('employee_attendances', [
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'status' => 'present',
        ]);
    }
}

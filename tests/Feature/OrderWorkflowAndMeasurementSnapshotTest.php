<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\GarmentType;
use App\Models\JobCard;
use App\Models\MeasurementProfile;
use App\Models\MeasurementTemplate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Tenant;
use App\Models\User;
use App\Services\OrderWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderWorkflowAndMeasurementSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_measurement_snapshots_remain_immutable_when_customer_profile_changes(): void
    {
        $tenant = Tenant::create([
            'name' => 'Royal Bespoke Atelier',
            'slug' => 'royal-bespoke',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'customer_code' => 'CUST-0099',
            'first_name' => 'Winston',
            'last_name' => 'Churchill',
            'phone' => '+44 20 7946 0991',
            'gender' => 'male',
        ]);

        $template = MeasurementTemplate::create([
            'tenant_id' => $tenant->id,
            'name' => "Men's Classic Suit",
            'gender' => 'male',
            'fields' => [
                ['key' => 'chest', 'label' => 'Chest', 'default_value' => 44],
                ['key' => 'waist', 'label' => 'Waist', 'default_value' => 40],
            ],
            'is_active' => true,
        ]);

        // Customer's initial profile: Chest 44.0, Waist 40.0
        $profile = MeasurementProfile::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'template_id' => $template->id,
            'profile_name' => 'Original Fit 1940',
            'values' => ['chest' => 44.0, 'waist' => 40.0],
            'is_default' => true,
        ]);

        $garment = GarmentType::create([
            'tenant_id' => $tenant->id,
            'name' => 'Three-Piece Pinstripe Suit',
            'slug' => 'three-piece-pinstripe-suit',
            'gender' => 'men',
            'base_price' => 1500.00,
            'is_active' => true,
        ]);

        $order = Order::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'order_number' => 'ORD-TEST-001',
            'order_type' => 'custom_tailoring',
            'status' => 'draft',
            'order_date' => now()->toDateString(),
            'expected_delivery_date' => now()->addDays(14)->toDateString(),
            'total_amount' => 1500.00,
            'balance_amount' => 1500.00,
        ]);

        // Freeze snapshot into OrderItem
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'garment_type_id' => $garment->id,
            'measurement_profile_id' => $profile->id,
            'item_name' => 'Pinstripe Jacket & Trousers',
            'quantity' => 1,
            'unit_price' => 1500.00,
            'total_price' => 1500.00,
            'measurement_snapshot' => [
                'profile_id' => $profile->id,
                'profile_name' => $profile->profile_name,
                'values' => $profile->values,
                'frozen_at' => now()->toIso8601String(),
            ],
            'status' => 'draft',
        ]);

        // Client changes weight 6 months later: Chest 48.0, Waist 44.0
        $profile->update([
            'values' => ['chest' => 48.0, 'waist' => 44.0],
            'profile_name' => 'Updated Fit 1945',
        ]);

        // Assert that the order item's measurement snapshot was completely untouched
        $freshOrderItem = $orderItem->fresh();
        $this->assertEquals(44.0, $freshOrderItem->measurement_snapshot['values']['chest']);
        $this->assertEquals(40.0, $freshOrderItem->measurement_snapshot['values']['waist']);
        $this->assertEquals('Original Fit 1940', $freshOrderItem->measurement_snapshot['profile_name']);
    }

    public function test_confirming_order_automatically_creates_production_job_cards(): void
    {
        $tenant = Tenant::create([
            'name' => 'Atelier Couture',
            'slug' => 'atelier-couture',
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Director Pierre',
            'email' => 'director@couture.test',
            'password' => Hash::make('password123'),
            'role' => 'business_owner',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'customer_code' => 'CUST-0088',
            'first_name' => 'Genevieve',
            'last_name' => 'Dubois',
            'phone' => '+33 1 42 68 00 00',
            'gender' => 'female',
        ]);

        $garment = GarmentType::create([
            'tenant_id' => $tenant->id,
            'name' => 'Evening Silk Gown',
            'slug' => 'evening-silk-gown',
            'gender' => 'women',
            'base_price' => 2200.00,
            'is_active' => true,
        ]);

        $order = Order::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'order_number' => 'ORD-PARIS-002',
            'order_type' => 'custom_tailoring',
            'status' => 'draft',
            'order_date' => now()->toDateString(),
            'expected_delivery_date' => now()->addDays(20)->toDateString(),
            'total_amount' => 2200.00,
            'balance_amount' => 2200.00,
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'garment_type_id' => $garment->id,
            'item_name' => 'Haute Couture Silk Gown',
            'quantity' => 1,
            'unit_price' => 2200.00,
            'total_price' => 2200.00,
            'status' => 'draft',
        ]);

        // Prior to confirmation: 0 Job cards
        $this->assertEquals(0, JobCard::where('order_id', $order->id)->count());

        // Perform workflow transition
        $workflowService = app(OrderWorkflowService::class);
        $workflowService->transitionStatus($order, 'confirmed', $user, 'Customer approved quotation deposit');

        // After confirmation: Order status is confirmed and 3 Job Cards (Cutting, Stitching, Finishing) are generated
        $this->assertEquals('confirmed', $order->fresh()->status);
        $this->assertEquals(3, JobCard::where('order_id', $order->id)->count());

        $jobCard = JobCard::where('order_id', $order->id)->first();
        $this->assertStringStartsWith('JOB-', $jobCard->job_number);
        $this->assertEquals($orderItem->id, $jobCard->order_item_id);
    }
}

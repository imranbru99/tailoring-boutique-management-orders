<?php

namespace Tests\Unit;

use App\Models\InventoryItem;
use App\Models\Tenant;
use App\Services\InventoryService;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingAndInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_service_calculates_total_and_estimated_margin(): void
    {
        $pricingService = new PricingService();

        $calculation = $pricingService->calculateItemPrice(
            materialCost: 80.00,
            laborCost: 120.00,
            additionalCost: 20.00,
            markupPercentage: 50.0,
            taxRate: 5.0,
            discountAmount: 10.00
        );

        // Base Cost: 80 + 120 + 20 = 220
        $this->assertEquals(220.00, $calculation['base_cost']);

        // Retail before discount: 220 * 1.5 = 330
        $this->assertEquals(330.00, $calculation['subtotal_before_discount']);

        // Discount: 10
        $this->assertEquals(10.00, $calculation['discount_amount']);

        // Subtotal: 320
        $this->assertEquals(320.00, $calculation['subtotal']);

        // Tax 5%: 16
        $this->assertEquals(16.00, $calculation['tax_amount']);

        // Total: 336
        $this->assertEquals(336.00, $calculation['total_price']);

        // Profit: Subtotal (320) - Base Cost (220) = 100
        $this->assertEquals(100.00, $calculation['estimated_profit']);
    }

    public function test_inventory_stock_movement_updates_stock_levels(): void
    {
        $tenant = Tenant::create([
            'name' => 'Fabric Atelier',
            'slug' => 'fabric-atelier',
            'is_active' => true,
        ]);

        $item = InventoryItem::create([
            'tenant_id' => $tenant->id,
            'name' => 'Italian Silk Thread',
            'sku' => 'THREAD-IT-01',
            'category' => 'threads',
            'unit' => 'spool',
            'cost_price' => 5.00,
            'current_stock' => 50,
            'reorder_level' => 10,
            'is_active' => true,
        ]);

        $service = app(InventoryService::class);

        // Record stock in
        $movement = $service->recordStockMovement(
            item: $item,
            movementType: 'purchase_in',
            quantity: 25,
            unitCost: 5.00,
            notes: 'Received new shipment'
        );

        $this->assertEquals(75, $item->fresh()->current_stock);
        $this->assertEquals('purchase_in', $movement->movement_type);

        // Record consumption
        $service->recordStockMovement(
            item: $item,
            movementType: 'order_consumption',
            quantity: -10,
            unitCost: 5.00,
            notes: 'Consumed for Order ORD-001'
        );

        $this->assertEquals(65, $item->fresh()->current_stock);
    }
}

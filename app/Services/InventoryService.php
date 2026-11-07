<?php

namespace App\Services;

use App\Models\FabricType;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\StockMovement;
use App\Models\StockReservation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    /**
     * Reserve fabric and materials for an order
     */
    public function reserveForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->fabric_type_id) {
                    $fabric = FabricType::find($item->fabric_type_id);
                    if ($fabric) {
                        // Estimated consumption: e.g. 2.5 meters per garment
                        $requiredQty = 2.5 * $item->quantity;

                        if ($fabric->current_stock < $requiredQty) {
                            throw new InvalidArgumentException(
                                "Insufficient fabric stock for [{$fabric->name}]. Available: {$fabric->current_stock} {$fabric->unit}, Required: {$requiredQty} {$fabric->unit}."
                            );
                        }

                        StockReservation::updateOrCreate(
                            [
                                'tenant_id' => $order->tenant_id,
                                'order_id' => $order->id,
                                'item_type' => 'fabric_type',
                                'item_id' => $fabric->id,
                            ],
                            [
                                'reserved_quantity' => $requiredQty,
                                'status' => 'reserved',
                            ]
                        );
                    }
                }
            }
        });
    }

    /**
     * Consume reserved stock upon production initiation
     */
    public function consumeForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $reservations = StockReservation::where('order_id', $order->id)
                ->where('status', 'reserved')
                ->get();

            foreach ($reservations as $reservation) {
                if ($reservation->item_type === 'fabric_type') {
                    $fabric = FabricType::find($reservation->item_id);
                    if ($fabric) {
                        $fabric->decrement('current_stock', (float) $reservation->reserved_quantity);

                        StockMovement::create([
                            'tenant_id' => $order->tenant_id,
                            'branch_id' => $order->branch_id,
                            'item_type' => 'fabric_type',
                            'item_id' => $fabric->id,
                            'movement_type' => 'order_consumption',
                            'quantity' => -$reservation->reserved_quantity,
                            'unit_cost' => $fabric->cost_per_unit,
                            'reference_type' => 'Order',
                            'reference_id' => $order->id,
                            'reason' => "Production material consumption for Order #{$order->order_number}",
                            'created_by' => auth()->id(),
                        ]);

                        $reservation->update(['status' => 'consumed']);
                    }
                }
            }
        });
    }

    /**
     * Record stock movement (purchase_in, manual adjustment, consumption, return)
     */
    public function recordStockMovement(
        InventoryItem|FabricType $item,
        string $movementType,
        float $quantity,
        ?float $unitCost = null,
        ?int $userId = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use ($item, $movementType, $quantity, $unitCost, $userId, $notes) {
            $itemType = $item instanceof FabricType ? 'fabric_type' : 'inventory_item';
            $cost = $unitCost ?? (float) ($item instanceof FabricType ? $item->cost_per_unit : $item->cost_price);

            // Update item current stock
            $item->current_stock += $quantity;
            $item->save();

            return StockMovement::create([
                'tenant_id' => $item->tenant_id,
                'branch_id' => $item->branch_id ?? null,
                'item_type' => $itemType,
                'item_id' => $item->id,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'unit_cost' => $cost,
                'reason' => $notes,
                'created_by' => $userId ?? auth()->id(),
            ]);
        });
    }

    /**
     * Log material wastage during cutting or stitching
     */
    public function recordWastage(
        int $tenantId,
        ?int $branchId,
        string $itemType,
        int $itemId,
        float $quantity,
        string $reason,
        ?int $orderId = null
    ): StockMovement {
        return DB::transaction(function () use ($tenantId, $branchId, $itemType, $itemId, $quantity, $reason, $orderId) {
            $unitCost = 0.0;

            if ($itemType === 'fabric_type') {
                $fabric = FabricType::findOrFail($itemId);
                $fabric->decrement('current_stock', $quantity);
                $unitCost = (float) $fabric->cost_per_unit;
            } elseif ($itemType === 'inventory_item') {
                $item = InventoryItem::findOrFail($itemId);
                $item->decrement('current_stock', $quantity);
                $unitCost = (float) $item->cost_price;
            }

            return StockMovement::create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'item_type' => $itemType,
                'item_id' => $itemId,
                'movement_type' => 'wastage',
                'quantity' => -$quantity,
                'unit_cost' => $unitCost,
                'reference_type' => $orderId ? 'Order' : 'WastageLog',
                'reference_id' => $orderId,
                'reason' => $reason,
                'created_by' => auth()->id(),
            ]);
        });
    }
}

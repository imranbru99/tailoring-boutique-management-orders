<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $query = InventoryItem::where('tenant_id', $tenantId)
            ->with(['supplier', 'branch'])
            ->latest();

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('quantity_on_hand', '<=', 'reorder_level');
        }

        $items = $query->paginate($request->get('per_page', 20));

        return response()->json($items);
    }

    public function adjust(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'type' => 'required|in:adjustment,purchase,wastage,return',
            'quantity' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $item = InventoryItem::where('tenant_id', $tenantId)->findOrFail($validated['inventory_item_id']);

        $movement = $this->inventoryService->recordStockMovement(
            $item,
            $validated['type'],
            $validated['quantity'],
            null,
            $request->user()->id,
            $validated['notes'] ?? 'Manual stock adjustment via API'
        );

        return response()->json([
            'message' => 'Stock adjusted successfully.',
            'inventory_item' => $item->fresh(),
            'movement' => $movement,
        ]);
    }
}

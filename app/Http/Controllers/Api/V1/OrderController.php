<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\MeasurementProfile;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderWorkflowService;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        protected OrderWorkflowService $workflowService,
        protected PricingService $pricingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $query = Order::where('tenant_id', $tenantId)
            ->with(['customer', 'branch', 'items.garmentType'])
            ->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        $orders = $query->paginate($request->get('per_page', 15));

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $branchId = $request->user()->current_branch_id ?? $request->get('branch_id');

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_type' => 'required|in:bespoke_tailoring,alteration,ready_to_wear,bulk_uniform',
            'priority' => 'required|in:standard,urgent,express',
            'due_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.garment_type_id' => 'required|exists:garment_types,id',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.measurement_profile_id' => 'nullable|exists:measurement_profiles,id',
            'items.*.fabric_type_id' => 'nullable|exists:fabric_types,id',
            'items.*.fabric_roll_id' => 'nullable|exists:fabric_rolls,id',
            'items.*.fabric_source' => 'required|in:boutique_inventory,customer_provided',
            'items.*.fabric_length_used' => 'nullable|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $tenantId, $branchId, $request) {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $order = Order::create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'customer_id' => $validated['customer_id'],
                'order_number' => $orderNumber,
                'order_type' => $validated['order_type'],
                'status' => 'draft',
                'priority' => $validated['priority'],
                'order_date' => now()->toDateString(),
                'due_date' => $validated['due_date'],
                'created_by_user_id' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'balance_amount' => 0,
                'payment_status' => 'unpaid',
            ]);

            $subtotal = 0;

            foreach ($validated['items'] as $itemData) {
                $measurementSnapshot = null;
                if (!empty($itemData['measurement_profile_id'])) {
                    $profile = MeasurementProfile::find($itemData['measurement_profile_id']);
                    if ($profile) {
                        $measurementSnapshot = [
                            'profile_id' => $profile->id,
                            'profile_name' => $profile->profile_name,
                            'template_name' => $profile->template?->name,
                            'values' => $profile->values,
                            'frozen_at' => now()->toIso8601String(),
                        ];
                    }
                }

                $itemSubtotal = $itemData['unit_price'] * $itemData['quantity'];
                $subtotal += $itemSubtotal;

                OrderItem::create([
                    'tenant_id' => $tenantId,
                    'order_id' => $order->id,
                    'garment_type_id' => $itemData['garment_type_id'],
                    'item_name' => $itemData['item_name'],
                    'fabric_type_id' => $itemData['fabric_type_id'] ?? null,
                    'fabric_roll_id' => $itemData['fabric_roll_id'] ?? null,
                    'fabric_source' => $itemData['fabric_source'],
                    'fabric_length_used' => $itemData['fabric_length_used'] ?? 0,
                    'measurement_profile_id' => $itemData['measurement_profile_id'] ?? null,
                    'measurement_snapshot' => $measurementSnapshot,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemSubtotal,
                    'total_price' => $itemSubtotal,
                    'special_instructions' => $itemData['special_instructions'] ?? null,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'balance_amount' => $subtotal,
            ]);

            return response()->json([
                'message' => 'Order created successfully.',
                'order' => $order->load(['customer', 'items.garmentType']),
            ], 201);
        });
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $order = Order::where('tenant_id', $tenantId)
            ->with(['customer', 'branch', 'items.garmentType', 'jobCards.assignedTailor', 'invoices', 'statusHistory.user'])
            ->findOrFail($id);

        return response()->json(['order' => $order]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $order = Order::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $this->workflowService->transitionStatus(
            $order,
            $validated['status'],
            $request->user(),
            $validated['notes'] ?? 'Updated via REST API'
        );

        return response()->json([
            'message' => "Order transitioned to {$validated['status']}.",
            'order' => $order->fresh(['items', 'jobCards']),
        ]);
    }
}

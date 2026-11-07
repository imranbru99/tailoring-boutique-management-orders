<?php

namespace App\Services;

use App\Models\JobCard;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\StockMovement;
use App\Models\StockReservation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderWorkflowService
{
    /**
     * Permitted state transitions map for tailoring orders
     */
    protected array $transitions = [
        'draft' => ['quotation', 'awaiting_advance', 'confirmed', 'cancelled'],
        'inquiry' => ['quotation', 'cancelled'],
        'quotation' => ['awaiting_approval', 'awaiting_advance', 'confirmed', 'cancelled'],
        'awaiting_approval' => ['awaiting_advance', 'confirmed', 'cancelled'],
        'awaiting_advance' => ['confirmed', 'cancelled'],
        'confirmed' => ['material_preparation', 'cutting', 'cancelled'],
        'material_preparation' => ['cutting', 'cancelled'],
        'cutting' => ['stitching', 'embroidery'],
        'stitching' => ['embroidery', 'finishing', 'fitting_required'],
        'embroidery' => ['stitching', 'finishing'],
        'finishing' => ['quality_inspection', 'fitting_required'],
        'quality_inspection' => ['final_approval', 'alteration_required', 'rework', 'ready_for_delivery'],
        'rework' => ['cutting', 'stitching', 'embroidery', 'finishing'],
        'fitting_required' => ['alteration_required', 'final_approval', 'ready_for_delivery'],
        'alteration_required' => ['stitching', 'finishing', 'quality_inspection'],
        'final_approval' => ['ready_for_delivery'],
        'ready_for_delivery' => ['dispatched', 'delivered'],
        'dispatched' => ['delivered', 'cancelled'],
        'delivered' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function canTransition(Order $order, string $newStatus): bool
    {
        if ($order->status === $newStatus) {
            return true;
        }

        // Allow business owner or super admin override, or check transition rules
        $allowed = $this->transitions[$order->status] ?? [];
        return in_array($newStatus, $allowed, true);
    }

    public function transitionStatus(Order $order, string $newStatus, $user = null, ?string $notes = null): Order
    {
        $userId = $user instanceof \App\Models\User ? $user->id : (is_numeric($user) ? (int) $user : auth()->id());
        return $this->transition($order, $newStatus, $notes, $userId);
    }

    public function transition(Order $order, string $newStatus, ?string $notes = null, ?int $userId = null, array $attachments = []): Order
    {
        if (!$this->canTransition($order, $newStatus)) {
            // Check if user is super admin/owner for emergency override
            $user = auth()->user();
            if (!$user || (!$user->isBusinessOwner() && !$user->isSuperAdmin())) {
                throw new InvalidArgumentException(
                    "Transition from [{$order->status}] to [{$newStatus}] is not permitted by tailoring workflow rules."
                );
            }
        }

        // Completion check for delivery
        if (in_array($newStatus, ['dispatched', 'delivered'], true)) {
            $this->validateDeliveryReadiness($order);
        }

        return DB::transaction(function () use ($order, $newStatus, $notes, $userId, $attachments) {
            $oldStatus = $order->status;
            $order->status = $newStatus;

            if ($newStatus === 'delivered' && !$order->actual_delivery_date) {
                $order->actual_delivery_date = now()->toDateString();
            }

            $order->save();

            // Record status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'previous_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $userId ?? auth()->id(),
                'notes' => $notes,
                'attachments' => $attachments,
            ]);

            // Lifecycle triggers
            if ($newStatus === 'confirmed') {
                $this->onOrderConfirmed($order);
            } elseif ($newStatus === 'delivered') {
                $this->onOrderDelivered($order);
            }

            return $order;
        });
    }

    /**
     * Ensure quality inspection passed or explicit waiver before delivery
     */
    protected function validateDeliveryReadiness(Order $order): void
    {
        // If order has quality inspections, ensure at least one passed and none currently failed
        $failedInspection = $order->qualityInspections()->where('status', 'failed')->exists();
        if ($failedInspection) {
            throw new InvalidArgumentException(
                "Order {$order->order_number} cannot be dispatched or delivered because a quality inspection defect is unresolved."
            );
        }
    }

    /**
     * Automatic generation of Job Cards for production team when order is confirmed
     */
    protected function onOrderConfirmed(Order $order): void
    {
        // 1. Generate Job Cards for each order item if none exist
        if ($order->jobCards()->doesntExist()) {
            foreach ($order->items as $item) {
                // Cutting stage
                JobCard::create([
                    'tenant_id' => $order->tenant_id,
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'job_number' => "JOB-{$order->order_number}-CUT",
                    'stage' => 'cutting',
                    'department' => 'cutting',
                    'priority' => $order->priority ?? 'normal',
                    'estimated_hours' => 1.5,
                    'status' => 'pending',
                    'instructions' => "Cut fabric for {$item->item_name}. Verify measurements from snapshot.",
                ]);

                // Stitching stage
                JobCard::create([
                    'tenant_id' => $order->tenant_id,
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'job_number' => "JOB-{$order->order_number}-STITCH",
                    'stage' => 'stitching',
                    'department' => 'tailoring',
                    'assigned_to' => $order->assigned_tailor_id,
                    'priority' => $order->priority ?? 'normal',
                    'estimated_hours' => 4.0,
                    'status' => 'pending',
                    'instructions' => "Assemble and stitch {$item->item_name} following customization details.",
                ]);

                // Finishing stage
                JobCard::create([
                    'tenant_id' => $order->tenant_id,
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'job_number' => "JOB-{$order->order_number}-FIN",
                    'stage' => 'finishing',
                    'department' => 'finishing',
                    'priority' => $order->priority ?? 'normal',
                    'estimated_hours' => 1.0,
                    'status' => 'pending',
                    'instructions' => "Pressing, buttonholes, trimming, and final presentation packaging.",
                ]);
            }
        }
    }

    /**
     * Handle stock movement and completion records upon delivery
     */
    protected function onOrderDelivered(Order $order): void
    {
        // Update customer balance if order still has pending dues
        $order->customer->increment('outstanding_balance', (float) $order->balance_amount);
    }
}

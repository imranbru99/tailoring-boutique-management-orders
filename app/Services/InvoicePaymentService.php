<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InvoicePaymentService
{
    /**
     * Create or retrieve an official invoice for an order
     */
    public function generateInvoiceForOrder(Order $order): Invoice
    {
        return DB::transaction(function () use ($order) {
            $existing = $order->invoices()->first();
            if ($existing) {
                return $existing;
            }

            $order->recalculateTotals();

            $year = now()->format('Y');
            $count = Invoice::where('tenant_id', $order->tenant_id)->count() + 1;
            $invoiceNumber = sprintf('INV-%s-%05d', $year, $count);

            $invoice = Invoice::create([
                'tenant_id' => $order->tenant_id,
                'branch_id' => $order->branch_id,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now()->toDateString(),
                'due_date' => $order->expected_delivery_date,
                'subtotal' => $order->total_material_cost + $order->total_labor_cost,
                'discount_amount' => $order->discount_amount,
                'tax_rate' => $order->tax_rate,
                'tax_amount' => $order->tax_amount,
                'total_amount' => $order->total_amount,
                'paid_amount' => $order->paid_amount,
                'balance_amount' => $order->balance_amount,
                'status' => $order->balance_amount <= 0 ? 'paid' : ($order->paid_amount > 0 ? 'partially_paid' : 'issued'),
                'notes' => $order->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($order->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);
            }

            return $invoice;
        });
    }

    /**
     * Record a payment (advance, installment, or refund) with transactional safety
     */
    public function recordPayment(
        int $tenantId,
        ?int $branchId,
        int $customerId,
        float $amount,
        string $paymentMethod = 'cash',
        ?int $orderId = null,
        ?int $invoiceId = null,
        bool $isAdvance = false,
        bool $isRefund = false,
        ?string $transactionRef = null,
        ?string $notes = null
    ): Payment {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        return DB::transaction(function () use (
            $tenantId, $branchId, $customerId, $amount, $paymentMethod,
            $orderId, $invoiceId, $isAdvance, $isRefund, $transactionRef, $notes
        ) {
            $year = now()->format('Y');
            $prefix = $isRefund ? 'REF' : 'PAY';
            $count = Payment::where('tenant_id', $tenantId)->count() + 1;
            $paymentNumber = sprintf('%s-%s-%05d', $prefix, $year, $count);

            $payment = Payment::create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'customer_id' => $customerId,
                'order_id' => $orderId,
                'invoice_id' => $invoiceId,
                'payment_number' => $paymentNumber,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'payment_date' => now()->toDateString(),
                'transaction_reference' => $transactionRef,
                'is_advance' => $isAdvance,
                'is_refund' => $isRefund,
                'notes' => $notes,
                'recorded_by' => auth()->id(),
            ]);

            // Sync order balances
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->recalculateTotals();
                }
            }

            // Sync invoice balances
            if ($invoiceId) {
                $invoice = Invoice::find($invoiceId);
                if ($invoice) {
                    $paid = $invoice->payments()->where('is_refund', false)->sum('amount')
                          - $invoice->payments()->where('is_refund', true)->sum('amount');
                    $bal = max(0, (float) $invoice->total_amount - $paid);
                    $status = $bal <= 0 ? 'paid' : ($paid > 0 ? 'partially_paid' : 'issued');
                    $invoice->update([
                        'paid_amount' => $paid,
                        'balance_amount' => $bal,
                        'status' => $status,
                    ]);
                }
            }

            return $payment;
        });
    }
}

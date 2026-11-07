<?php

namespace App\Filament\App\Resources\PaymentResource\Pages;

use App\Filament\App\Resources\PaymentResource;
use App\Models\Invoice;
use App\Models\Order;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->order_id) {
            $order = Order::find($this->record->order_id);
            $order?->recalculateTotals();
        }

        if ($this->record->invoice_id) {
            $invoice = Invoice::find($this->record->invoice_id);
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
    }
}

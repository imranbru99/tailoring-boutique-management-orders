<?php

namespace App\Filament\App\Pages;

use App\Core\Modules\ModuleManager;
use App\Models\BoutiqueProduct;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InvoicePaymentService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class PosTerminalPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Sales & Finance';
    protected static ?string $navigationLabel = 'Point of Sale (POS)';
    protected static ?string $title = 'Boutique Point of Sale Counter';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.app.pages.pos-terminal-page';

    public ?int $selectedCustomerId = null;
    public string $searchQuery = '';
    public array $cart = [];
    public float $discountAmount = 0.0;
    public float $taxRate = 5.0;
    public string $paymentMethod = 'cash';
    public float $tenderedAmount = 0.0;

    public static function shouldRegisterNavigation(): bool
    {
        return app(ModuleManager::class)->isEnabled('pos');
    }

    public function mount(): void
    {
        // Default to first customer or create walk-in
        $walkIn = Customer::firstOrCreate(
            ['tenant_id' => auth()->user()?->tenant_id, 'customer_code' => 'WALK-IN'],
            [
                'first_name' => 'Walk-in',
                'last_name' => 'Client',
                'phone' => '000-000-0000',
                'customer_type' => 'regular',
            ]
        );

        $this->selectedCustomerId = $walkIn->id;
    }

    public function addToCart(int $productId): void
    {
        $product = BoutiqueProduct::find($productId);
        if (!$product) {
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['total'] = $this->cart[$productId]['quantity'] * $this->cart[$productId]['price'];
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->retail_price,
                'quantity' => 1,
                'total' => (float) $product->retail_price,
            ];
        }

        Notification::make()
            ->title('Item Added to Cart')
            ->body("{$product->name} added.")
            ->success()
            ->send();
    }

    public function updateQuantity(int $productId, int $qty): void
    {
        if ($qty <= 0) {
            unset($this->cart[$productId]);
        } elseif (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity'] = $qty;
            $this->cart[$productId]['total'] = $qty * $this->cart[$productId]['price'];
        }
    }

    public function getSubtotalProperty(): float
    {
        return array_sum(array_column($this->cart, 'total'));
    }

    public function getTaxAmountProperty(): float
    {
        $taxable = max(0, $this->subtotal - $this->discountAmount);
        return round($taxable * ($this->taxRate / 100), 2);
    }

    public function getGrandTotalProperty(): float
    {
        return max(0, round($this->subtotal - $this->discountAmount + $this->taxAmount, 2));
    }

    public function processSale(InvoicePaymentService $paymentService): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Cart is Empty')->danger()->send();
            return;
        }

        $tenantId = auth()->user()?->tenant_id;
        $customer = Customer::find($this->selectedCustomerId);

        DB::transaction(function () use ($tenantId, $customer, $paymentService) {
            $orderNumber = 'POS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $order = Order::create([
                'tenant_id' => $tenantId,
                'branch_id' => auth()->user()?->current_branch_id,
                'customer_id' => $customer->id,
                'order_number' => $orderNumber,
                'order_type' => 'ready_made',
                'status' => 'delivered',
                'order_date' => now()->toDateString(),
                'expected_delivery_date' => now()->toDateString(),
                'actual_delivery_date' => now()->toDateString(),
                'discount_amount' => $this->discountAmount,
                'tax_rate' => $this->taxRate,
                'tax_amount' => $this->taxAmount,
                'total_amount' => $this->grandTotal,
                'paid_amount' => $this->grandTotal,
                'balance_amount' => 0.00,
                'payment_status' => 'paid',
                'created_by' => auth()->id(),
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['total'],
                    'status' => 'delivered',
                ]);

                // Decrement boutique product inventory
                BoutiqueProduct::where('id', $item['id'])->decrement('stock_quantity', $item['quantity']);
            }

            // Generate invoice
            $invoice = $paymentService->generateInvoiceForOrder($order);

            // Record payment
            $paymentService->recordPayment(
                $tenantId,
                auth()->user()?->current_branch_id,
                $customer->id,
                $this->grandTotal,
                $this->paymentMethod,
                $order->id,
                $invoice->id,
                false,
                false,
                'POS-' . uniqid()
            );

            Notification::make()
                ->title('Sale Finalized Successfully!')
                ->body("Order #{$orderNumber} closed. Invoice #{$invoice->invoice_number} generated.")
                ->success()
                ->persistent()
                ->send();

            // Clear cart
            $this->cart = [];
            $this->discountAmount = 0.0;
        });
    }

    public function getAvailableProductsProperty()
    {
        $tenantId = auth()->user()?->tenant_id;
        $q = BoutiqueProduct::where('tenant_id', $tenantId)->where('is_active', true);

        if (!empty($this->searchQuery)) {
            $q->where(function ($sub) {
                $sub->where('name', 'like', "%{$this->searchQuery}%")
                    ->orWhere('sku', 'like', "%{$this->searchQuery}%")
                    ->orWhere('barcode', 'like', "%{$this->searchQuery}%");
            });
        }

        return $q->take(12)->get();
    }
}

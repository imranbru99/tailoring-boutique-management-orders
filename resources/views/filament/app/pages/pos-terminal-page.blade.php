<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Products Grid (8 cols) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">
            <!-- Search Bar -->
            <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-3">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                <input
                    wire:model.live.debounce.250ms="searchQuery"
                    type="text"
                    placeholder="Search boutique items by name, SKU, or scan barcode..."
                    class="w-full bg-transparent border-0 focus:ring-0 text-sm placeholder-gray-400"
                >
            </div>

            <!-- Product Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($this->availableProducts as $prod)
                    <div
                        wire:click="addToCart({{ $prod->id }})"
                        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl p-4 cursor-pointer transition-all shadow-sm hover:shadow-md flex flex-col justify-between"
                    >
                        <div>
                            <div class="w-full h-24 bg-gray-100 dark:bg-gray-800 rounded-xl mb-3 flex items-center justify-center text-gray-400">
                                <x-heroicon-o-shopping-bag class="w-8 h-8 text-emerald-500" />
                            </div>
                            <h4 class="font-bold text-xs text-gray-900 dark:text-gray-100 line-clamp-2">{{ $prod->name }}</h4>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $prod->sku ?? 'NO-SKU' }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($prod->retail_price, 2) }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-500">{{ $prod->stock_quantity }} in stock</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-400 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800">
                        <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mx-auto text-gray-300 mb-2" />
                        <p class="text-sm">No ready-made boutique items found matching query.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- POS Cart & Checkout (5 cols) -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-shopping-cart class="w-5 h-5 text-emerald-600" />
                        <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">Cart Register</h3>
                    </div>
                    <span class="text-xs bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold px-2 py-0.5 rounded-full">
                        {{ count($cart) }} Items
                    </span>
                </div>

                <!-- Customer Selection -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Customer</label>
                    <select wire:model.live="selectedCustomerId" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        @foreach(\App\Models\Customer::where('tenant_id', auth()->user()?->tenant_id)->get() as $cust)
                            <option value="{{ $cust->id }}">{{ $cust->full_name }} ({{ $cust->customer_code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Cart Items List -->
                <div class="max-h-64 overflow-y-auto space-y-2 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($cart as $pId => $item)
                        <div class="pt-2 flex items-center justify-between text-xs">
                            <div class="flex-1 pr-2">
                                <span class="font-bold text-gray-800 dark:text-gray-200 block">{{ $item['name'] }}</span>
                                <span class="text-gray-400">${{ number_format($item['price'], 2) }} each</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="updateQuantity({{ $pId }}, {{ $item['quantity'] - 1 }})"
                                    class="w-6 h-6 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 flex items-center justify-center font-bold"
                                >-</button>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $item['quantity'] }}</span>
                                <button
                                    wire:click="updateQuantity({{ $pId }}, {{ $item['quantity'] + 1 }})"
                                    class="w-6 h-6 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 flex items-center justify-center font-bold"
                                >+</button>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 w-16 text-right">
                                    ${{ number_format($item['total'], 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400 text-xs">
                            Cart is empty. Click any item on the left to add.
                        </div>
                    @endforelse
                </div>

                <!-- Financial Calculations -->
                <div class="border-t border-gray-100 dark:border-gray-800 pt-3 space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal:</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">${{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-500">
                        <span>Discount ($):</span>
                        <input wire:model.live.debounce.300ms="discountAmount" type="number" step="0.5" class="w-20 text-xs text-right py-1 rounded border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Tax ({{ $taxRate }}%):</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">+${{ number_format($this->taxAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-gray-900 dark:text-gray-100 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span>Grand Total:</span>
                        <span class="text-emerald-600 dark:text-emerald-400">${{ number_format($this->grandTotal, 2) }}</span>
                    </div>
                </div>

                <!-- Payment Tender -->
                <div class="border-t border-gray-100 dark:border-gray-800 pt-3 space-y-2">
                    <label class="block text-xs font-semibold text-gray-500">Tender Method</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['cash' => 'Cash', 'card' => 'Card POS', 'bkash' => 'Mobile Pay'] as $mKey => $mLabel)
                            <button
                                wire:click="$set('paymentMethod', '{{ $mKey }}')"
                                type="button"
                                class="py-2 text-xs font-bold rounded-xl border transition-all {{ $paymentMethod === $mKey ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300' }}"
                            >
                                {{ $mLabel }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Complete Button -->
                <button
                    wire:click="processSale"
                    type="button"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2"
                >
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                    <span>Pay ${{ number_format($this->grandTotal, 2) }} & Finalize Sale</span>
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>

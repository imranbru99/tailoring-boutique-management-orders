<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('tax_id')->nullable();
            $table->decimal('outstanding_balance', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('category')->default('accessories'); // buttons, threads, zippers, lace, lining, interlining, hooks, packaging, raw_materials
            $table->string('unit')->default('piece'); // piece, spool, meter, yard, packet, gross
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('reorder_level', 12, 2)->default(10);
            $table->string('storage_location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'sku']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('item_type'); // fabric_type, inventory_item, boutique_product
            $table->unsignedBigInteger('item_id');
            $table->string('movement_type'); // purchase_in, order_consumption, wastage, stock_adjustment, customer_return, transfer_in, transfer_out
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->string('reference_type')->nullable(); // Order, PurchaseOrder, Adjustment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'item_type', 'item_id']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('item_type'); // fabric_type, inventory_item
            $table->unsignedBigInteger('item_id');
            $table->decimal('reserved_quantity', 12, 2);
            $table->string('status')->default('reserved'); // reserved, consumed, released
            $table->timestamps();

            $table->index(['tenant_id', 'order_id']);
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('po_number');
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('status')->default('draft'); // draft, ordered, partially_received, received, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'po_number']);
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->string('item_type'); // fabric_type, inventory_item
            $table->unsignedBigInteger('item_id');
            $table->string('item_name');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->decimal('received_quantity', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stock_reservations');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('suppliers');
    }
};

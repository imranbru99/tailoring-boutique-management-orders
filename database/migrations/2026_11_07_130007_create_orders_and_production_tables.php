<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('order_number');
            $table->string('order_type')->default('custom_tailoring'); 
            // custom_tailoring, ready_made, alteration, boutique, bridal, uniform, corporate, embroidery
            $table->string('status')->default('inquiry');
            // inquiry, quotation, awaiting_approval, awaiting_advance, confirmed, material_preparation,
            // cutting, stitching, embroidery, finishing, quality_inspection, fitting_required,
            // alteration_required, final_approval, ready_for_delivery, dispatched, delivered, completed, cancelled
            $table->date('order_date');
            $table->date('expected_delivery_date');
            $table->date('actual_delivery_date')->nullable();
            $table->decimal('total_material_cost', 12, 2)->default(0);
            $table->decimal('total_labor_cost', 12, 2)->default(0);
            $table->decimal('additional_charges', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);
            $table->string('payment_status')->default('unpaid'); // unpaid, partial, paid, refunded
            $table->foreignId('assigned_tailor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_designer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'order_number']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'order_date']);
            $table->index(['tenant_id', 'expected_delivery_date']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('garment_type_id')->nullable()->constrained('garment_types')->nullOnDelete();
            $table->foreignId('design_catalog_id')->nullable()->constrained('design_catalogs')->nullOnDelete();
            $table->foreignId('fabric_type_id')->nullable()->constrained('fabric_types')->nullOnDelete();
            $table->foreignId('measurement_profile_id')->nullable()->constrained('measurement_profiles')->nullOnDelete();
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('material_cost', 12, 2)->default(0);
            $table->decimal('labor_cost', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            // CRITICAL: Immutable snapshot of measurements at order time
            $table->json('measurement_snapshot')->nullable();
            $table->json('customization_details')->nullable(); // style options, special collar/cuff/pleat requests
            $table->string('status')->default('confirmed');
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });

        Schema::create('job_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->string('job_number');
            $table->string('stage')->default('cutting'); // cutting, stitching, embroidery, finishing
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('department')->default('tailoring');
            $table->string('priority')->default('normal');
            $table->decimal('estimated_hours', 8, 2)->default(0);
            $table->decimal('actual_hours', 8, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed, paused, rework
            $table->text('instructions')->nullable();
            $table->text('tailor_notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'job_number']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('production_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->cascadeOnDelete();
            $table->string('task_name');
            $table->integer('sequence')->default(1);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_tasks');
        Schema::dropIfExists('job_cards');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

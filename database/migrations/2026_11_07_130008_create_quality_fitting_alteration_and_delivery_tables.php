<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('inspection_date');
            $table->string('status')->default('passed'); // passed, failed, rework_required
            $table->json('checklist_results')->nullable(); // stitching, measurements, fabric_quality, finishing, design_accuracy
            $table->text('defect_notes')->nullable();
            $table->json('defect_photos')->nullable();
            $table->text('action_taken')->nullable();
            $table->timestamps();
        });

        Schema::create('fittings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->integer('round_number')->default(1);
            $table->dateTime('fitting_date');
            $table->foreignId('fitter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('scheduled'); // scheduled, completed, alteration_required, approved, cancelled
            $table->text('customer_feedback')->nullable();
            $table->text('adjustment_instructions')->nullable();
            $table->boolean('requires_alteration')->default(false);
            $table->timestamps();
        });

        Schema::create('alterations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('fitting_id')->nullable()->constrained('fittings')->nullOnDelete();
            $table->string('alteration_number');
            $table->text('reason');
            $table->text('adjustment_notes');
            $table->decimal('additional_charge', 12, 2)->default(0);
            $table->foreignId('assigned_tailor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->date('expected_completion_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'alteration_number']);
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('delivery_number');
            $table->string('delivery_type')->default('shop_pickup'); // shop_pickup, home_delivery, courier
            $table->string('status')->default('ready'); // ready, dispatched, delivered, failed, returned
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('delivery_address')->nullable();
            $table->foreignId('delivery_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('courier_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->date('scheduled_date');
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('proof_of_delivery_url')->nullable();
            $table->string('signature_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'delivery_number']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('alterations');
        Schema::dropIfExists('fittings');
        Schema::dropIfExists('quality_inspections');
    }
};

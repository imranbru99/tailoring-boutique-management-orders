<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('appointment_type')->default('measurement'); 
            // measurement, design_consultation, fitting, final_fitting, alteration, styling
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('scheduled'); 
            // scheduled, confirmed, in_progress, completed, cancelled, no_show
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'appointment_date']);
            $table->index(['tenant_id', 'assigned_staff_id', 'appointment_date']);
        });

        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // Silver, Gold, Platinum, VIP Couture
            $table->string('code')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('min_spend_required', 12, 2)->default(0);
            $table->decimal('points_multiplier', 4, 2)->default(1.0);
            $table->json('benefits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('customer_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('membership_plans')->cascadeOnDelete();
            $table->string('card_number')->nullable();
            $table->integer('points_accumulated')->default(0);
            $table->integer('points_redeemed')->default(0);
            $table->date('starts_at');
            $table->date('expires_at')->nullable();
            $table->string('status')->default('active'); // active, expired, suspended
            $table->timestamps();

            $table->unique(['tenant_id', 'customer_id']);
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('code');
            $table->string('discount_type')->default('percentage'); // percentage, fixed
            $table->decimal('discount_value', 12, 2);
            $table->decimal('min_spend', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('channel')->default('sms'); // sms, email, whatsapp
            $table->string('audience_type')->default('all_customers'); // all, vip, inactive, bridal
            $table->text('message');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('recipients_count')->default(0);
            $table->string('status')->default('draft'); // draft, scheduled, sent, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('customer_memberships');
        Schema::dropIfExists('membership_plans');
        Schema::dropIfExists('appointments');
    }
};

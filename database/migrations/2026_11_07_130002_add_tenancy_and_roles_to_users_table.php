<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->foreignId('current_branch_id')->nullable()->after('tenant_id')->constrained('branches')->nullOnDelete();
            $table->string('role')->default('business_owner')->after('email'); // super_admin, business_owner, general_manager, branch_manager, receptionist, tailor, cutter, fashion_designer, quality_inspector, inventory_manager, accountant, hr_manager, delivery_staff, customer
            $table->string('phone')->nullable()->after('role');
            $table->string('avatar_url')->nullable()->after('phone');
            $table->string('job_title')->nullable()->after('avatar_url');
            $table->string('locale', 10)->default('en')->after('job_title');
            $table->decimal('hourly_rate', 10, 2)->default(0)->after('locale');
            $table->decimal('commission_rate', 5, 2)->default(0)->after('hourly_rate');
            $table->json('skills')->nullable()->after('commission_rate');
            $table->boolean('is_active')->default(true)->after('skills');
            $table->softDeletes();
        });

        Schema::create('branch_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['branch_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_user');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['current_branch_id']);
            $table->dropColumn([
                'tenant_id',
                'current_branch_id',
                'role',
                'phone',
                'avatar_url',
                'job_title',
                'locale',
                'hourly_rate',
                'commission_rate',
                'skills',
                'is_active',
                'deleted_at',
            ]);
        });
    }
};

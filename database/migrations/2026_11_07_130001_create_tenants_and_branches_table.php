<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('mode')->default('single_business'); // single_business, multi_branch, saas, enterprise, demo
            $table->string('business_type')->default('boutique'); // tailoring_shop, boutique, fashion_studio, alteration_shop, bridal_studio, uniform_manufacturer
            $table->string('currency', 3)->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            $table->string('timezone')->default('UTC');
            $table->string('date_format')->default('Y-m-d');
            $table->string('locale', 10)->default('en');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('measurement_unit')->default('inch'); // inch, cm
            $table->boolean('is_active')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::dropIfExists('tenants');
    }
};

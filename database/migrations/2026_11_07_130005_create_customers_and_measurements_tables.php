<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Linked portal user account
            $table->string('customer_code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('full_name')->virtualAs("CONCAT(first_name, ' ', COALESCE(last_name, ''))");
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('alt_phone')->nullable();
            $table->string('gender')->nullable(); // male, female, other, child
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('customer_type')->default('regular'); // regular, vip, wholesale, bridal, corporate
            $table->string('preferred_communication')->default('phone'); // phone, email, sms, whatsapp
            $table->json('tags')->nullable();
            $table->json('preferences')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_url')->nullable();
            $table->decimal('outstanding_balance', 12, 2)->default(0);
            $table->integer('loyalty_points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'customer_code']);
            $table->index(['tenant_id', 'phone']);
            $table->index(['tenant_id', 'email']);
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('label')->default('Home');
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('measurement_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // e.g. Men Shirt & Trouser, Women Blouse & Lehenga, Salwar Kameez, Blazer
            $table->string('code')->nullable();
            $table->string('gender')->default('all'); // male, female, children, all
            $table->string('garment_category')->default('general');
            $table->string('unit')->default('inch'); // inch, cm
            $table->json('fields'); // array of field definitions: key, label, category, unit, default_value, is_required, instructions, diagram_url
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('measurement_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('measurement_templates')->cascadeOnDelete();
            $table->string('profile_name'); // e.g. Slim Fit Punjabi, Wedding Sherwani, Regular Office Shirt
            $table->string('unit')->default('inch');
            $table->json('values'); // key-value pairs matching template fields
            $table->text('fitting_notes')->nullable();
            $table->foreignId('measured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('version')->default(1);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'customer_id']);
        });

        Schema::create('measurement_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('measurement_profile_id')->constrained('measurement_profiles')->cascadeOnDelete();
            $table->integer('version_number');
            $table->string('unit')->default('inch');
            $table->json('values');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('change_reason')->nullable();
            $table->timestamps();

            $table->unique(['measurement_profile_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurement_versions');
        Schema::dropIfExists('measurement_profiles');
        Schema::dropIfExists('measurement_templates');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
    }
};

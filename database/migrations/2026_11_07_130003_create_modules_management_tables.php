<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->default('operations'); // core, operations, production, sales, finance, hr, ai, marketing, system
            $table->string('version')->default('1.0.0');
            $table->string('icon')->nullable();
            $table->boolean('is_core')->default(false); // Core modules cannot be disabled
            $table->json('dependencies')->nullable(); // Keys of required modules
            $table->json('optional_dependencies')->nullable();
            $table->json('permissions')->nullable();
            $table->string('min_plan')->default('free');
            $table->boolean('is_active')->default(true); // Global catalog availability
            $table->timestamps();
        });

        Schema::create('tenant_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('module_key');
            $table->boolean('is_enabled')->default(true);
            $table->json('settings')->nullable();
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'module_key']);
            $table->foreign('module_key')->references('key')->on('modules')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_modules');
        Schema::dropIfExists('modules');
    }
};

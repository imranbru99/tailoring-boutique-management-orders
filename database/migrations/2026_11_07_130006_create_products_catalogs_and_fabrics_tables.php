<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('garment_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('measurement_template_id')->nullable()->constrained('measurement_templates')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('gender')->default('all'); // men, women, kids, unisex
            $table->decimal('base_price', 12, 2)->default(0);
            $table->integer('estimated_days')->default(7);
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->json('customization_options')->nullable(); // e.g. Collar types, cuff styles, pleats, vents, lining options
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('design_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('garment_type_id')->constrained('garment_types')->cascadeOnDelete();
            $table->string('title');
            $table->string('code')->nullable();
            $table->string('slug');
            $table->string('collection_name')->nullable(); // Summer 2026, Bridal Couture, Eid Special
            $table->text('description')->nullable();
            $table->string('front_image_url')->nullable();
            $table->string('back_image_url')->nullable();
            $table->string('neck_design')->nullable();
            $table->string('collar_design')->nullable();
            $table->string('sleeve_design')->nullable();
            $table->string('pocket_design')->nullable();
            $table->string('embroidery_design')->nullable();
            $table->text('stitching_instructions')->nullable();
            $table->decimal('additional_price', 12, 2)->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('fabric_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('material_type')->default('cotton'); // cotton, silk, linen, wool, chiffon, georgette, velvet, denim, polyester, blended
            $table->string('color')->nullable();
            $table->string('color_code', 10)->nullable(); // Hex color
            $table->string('pattern')->nullable(); // solid, striped, checked, floral, printed, embroidered
            $table->string('width')->nullable(); // 44 inch, 58 inch, etc.
            $table->string('unit')->default('meter'); // meter, yard, piece, roll, kg
            $table->decimal('cost_per_unit', 12, 2)->default(0);
            $table->decimal('selling_price_per_unit', 12, 2)->default(0);
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('reorder_level', 12, 2)->default(10);
            $table->string('storage_location')->nullable(); // Rack A, Shelf 3
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('fabric_rolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('fabric_type_id')->constrained('fabric_types')->cascadeOnDelete();
            $table->string('roll_number');
            $table->string('batch_number')->nullable();
            $table->decimal('initial_quantity', 12, 2);
            $table->decimal('current_quantity', 12, 2);
            $table->string('unit')->default('meter');
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->string('status')->default('in_stock'); // in_stock, reserved, depleted
            $table->timestamps();

            $table->unique(['tenant_id', 'roll_number']);
        });

        Schema::create('boutique_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('slug');
            $table->string('barcode')->nullable();
            $table->string('size')->nullable(); // S, M, L, XL, Free Size
            $table->string('color')->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('retail_price', 12, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->integer('reorder_point')->default(5);
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'sku']);
            $table->index(['tenant_id', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boutique_products');
        Schema::dropIfExists('fabric_rolls');
        Schema::dropIfExists('fabric_types');
        Schema::dropIfExists('design_catalogs');
        Schema::dropIfExists('garment_types');
        Schema::dropIfExists('product_categories');
    }
};

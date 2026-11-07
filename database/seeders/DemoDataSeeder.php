<?php

namespace Database\Seeders;

use App\Core\Modules\ModuleManager;
use App\Core\Modules\ModuleRegistry;
use App\Models\Alteration;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Delivery;
use App\Models\EmployeeAttendance;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FabricRoll;
use App\Models\FabricType;
use App\Models\Fitting;
use App\Models\GarmentType;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobCard;
use App\Models\MeasurementProfile;
use App\Models\MeasurementTemplate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\QualityInspection;
use App\Models\Shift;
use App\Models\SubscriptionPlan;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Demo@12345!');

        // 1. Create SaaS Subscription Plans
        SubscriptionPlan::create([
            'name' => 'Atelier Starter',
            'slug' => 'atelier-starter',
            'description' => 'For independent tailor masters & single boutique studios.',
            'price_monthly' => 29.00,
            'price_yearly' => 290.00,
            'max_branches' => 1,
            'max_staff' => 3,
            'max_orders_per_month' => 150,
            'max_storage_mb' => 2048,
            'features' => ['Core Customers & Measurements', 'Standard Invoicing', 'Single Atelier Branch', 'Email Notifications'],
            'is_featured' => false,
            'is_active' => true,
        ]);

        SubscriptionPlan::create([
            'name' => 'Boutique Professional',
            'slug' => 'boutique-professional',
            'description' => 'For growing designer boutiques & multi-room tailoring houses.',
            'price_monthly' => 79.00,
            'price_yearly' => 790.00,
            'max_branches' => 3,
            'max_staff' => 15,
            'max_orders_per_month' => 1000,
            'max_storage_mb' => 10240,
            'features' => ['All 30 Modules Included', 'Multi-Branch Scoping', 'Biometric Attendance Hub', 'AI Fabric Catalog Studio', 'Unicode Multi-script PDFs'],
            'is_featured' => true,
            'is_active' => true,
        ]);

        SubscriptionPlan::create([
            'name' => 'Enterprise Couture',
            'slug' => 'enterprise-couture',
            'description' => 'For international fashion brands & luxury franchise networks.',
            'price_monthly' => 199.00,
            'price_yearly' => 1990.00,
            'max_branches' => 25,
            'max_staff' => 100,
            'max_orders_per_month' => 10000,
            'max_storage_mb' => 51200,
            'features' => ['Unlimited Scale', 'Dedicated Cloud Instance', 'Custom ERP & POS Webhooks', 'Priority 24/7 SLA', 'Custom AI Fine-tuning'],
            'is_featured' => false,
            'is_active' => true,
        ]);

        // 2. Persona 1: Platform Super Admin
        User::create([
            'name' => 'Imran Ahmed (Platform Admin)',
            'email' => 'platform-admin@tailor.test',
            'password' => $password,
            'role' => 'super_admin',
            'job_title' => 'Chief Platform Architect',
            'phone' => '+880 1700 000001',
            'is_active' => true,
        ]);

        // 3. Fictional Flagship Tenant: Elegant Stitch Boutique
        $tenant = Tenant::create([
            'name' => 'Elegant Stitch Boutique',
            'slug' => 'elegant-stitch-boutique',
            'business_type' => 'boutique',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'measurement_unit' => 'inch',
            'timezone' => 'Asia/Dhaka',
            'phone' => '+880 1812 345678',
            'email' => 'contact@elegantstitch.com',
            'address' => 'Road 11, Block D, Banani',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'mode' => 'multi_branch',
            'is_active' => true,
            'trial_ends_at' => now()->addYear(),
            'settings' => [
                'website' => 'https://imrandev.bd',
                'lead_architect' => 'Imran Ahmed',
            ],
        ]);

        // Enable all 30 Modules for Tenant
        $moduleManager = app(ModuleManager::class);
        $moduleManager->syncCatalogToDatabase();
        foreach (ModuleRegistry::all() as $key => $module) {
            $moduleManager->enable($key, $tenant->id);
        }

        // 4. Create 2 Physical Branches
        $branchMain = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Main Atelier (Gulshan)',
            'code' => 'GULSHAN',
            'phone' => '+880 1812 345671',
            'email' => 'gulshan@elegantstitch.com',
            'address' => 'Avenue 5, Gulshan-2',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'is_main' => true,
            'is_active' => true,
        ]);

        $branchDowntown = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Downtown Studio (Banani)',
            'code' => 'BANANI',
            'phone' => '+880 1812 345672',
            'email' => 'banani@elegantstitch.com',
            'address' => 'Road 11, Banani',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'is_main' => false,
            'is_active' => true,
        ]);

        // 5. Create Staff Personas (Roles 2 to 7)
        $owner = User::create([
            'tenant_id' => $tenant->id,
            'current_branch_id' => $branchMain->id,
            'name' => 'Eleanor Vance (Boutique Owner)',
            'email' => 'owner@tailor.test',
            'password' => $password,
            'role' => 'business_owner',
            'job_title' => 'Founder & Creative Director',
            'phone' => '+880 1812 000001',
            'is_active' => true,
        ]);
        $owner->branches()->attach([$branchMain->id => ['is_primary' => true], $branchDowntown->id => ['is_primary' => false]]);

        $manager = User::create([
            'tenant_id' => $tenant->id,
            'current_branch_id' => $branchMain->id,
            'name' => 'Marcus Sterling (Branch Manager)',
            'email' => 'manager@tailor.test',
            'password' => $password,
            'role' => 'manager',
            'job_title' => 'General Atelier Manager',
            'phone' => '+880 1812 000002',
            'is_active' => true,
        ]);
        $manager->branches()->attach([$branchMain->id => ['is_primary' => true], $branchDowntown->id => ['is_primary' => false]]);

        $tailor = User::create([
            'tenant_id' => $tenant->id,
            'current_branch_id' => $branchMain->id,
            'name' => 'Master Kabir Hossain (Head Tailor)',
            'email' => 'tailor@tailor.test',
            'password' => $password,
            'role' => 'tailor',
            'job_title' => 'Master Tailor & Pattern Cutter',
            'phone' => '+880 1812 000003',
            'hourly_rate' => 25.00,
            'skills' => ['Bespoke Suit Cutting', 'Sherwani Gold Zari', 'Lapel Hand Stitching', 'Anatomical Fitting'],
            'is_active' => true,
        ]);
        $tailor->branches()->attach([$branchMain->id => ['is_primary' => true]]);

        $receptionist = User::create([
            'tenant_id' => $tenant->id,
            'current_branch_id' => $branchMain->id,
            'name' => 'Sophia Lin (Front Desk Concierge)',
            'email' => 'reception@tailor.test',
            'password' => $password,
            'role' => 'receptionist',
            'job_title' => 'Client Experience Executive',
            'phone' => '+880 1812 000004',
            'is_active' => true,
        ]);
        $receptionist->branches()->attach([$branchMain->id => ['is_primary' => true]]);

        $storekeeper = User::create([
            'tenant_id' => $tenant->id,
            'current_branch_id' => $branchMain->id,
            'name' => 'Tariq Al-Mansoor (Inventory Master)',
            'email' => 'inventory@tailor.test',
            'password' => $password,
            'role' => 'inventory_manager',
            'job_title' => 'Fabric & Haberdashery Controller',
            'phone' => '+880 1812 000005',
            'is_active' => true,
        ]);
        $storekeeper->branches()->attach([$branchMain->id => ['is_primary' => true]]);

        $accountant = User::create([
            'tenant_id' => $tenant->id,
            'current_branch_id' => $branchMain->id,
            'name' => 'Nadia Rahman (Chief Cashier)',
            'email' => 'accountant@tailor.test',
            'password' => $password,
            'role' => 'accountant',
            'job_title' => 'Finance & POS Controller',
            'phone' => '+880 1812 000006',
            'is_active' => true,
        ]);
        $accountant->branches()->attach([$branchMain->id => ['is_primary' => true]]);

        // 6. Persona 8: Customer User (Self-service portal)
        $customerUser = User::create([
            'tenant_id' => $tenant->id,
            'current_branch_id' => $branchMain->id,
            'name' => 'Sir Julian Montgomery (VIP Client)',
            'email' => 'customer@tailor.test',
            'password' => $password,
            'role' => 'customer',
            'job_title' => 'Patron',
            'phone' => '+880 1711 998877',
            'is_active' => true,
        ]);

        // Customer Profile Record for Julian
        $vipCustomer = Customer::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'user_id' => $customerUser->id,
            'customer_code' => 'CUST-VIP-001',
            'first_name' => 'Julian',
            'last_name' => 'Montgomery',
            'email' => 'customer@tailor.test',
            'phone' => '+880 1711 998877',
            'gender' => 'male',
            'date_of_birth' => '1985-06-15',
            'customer_type' => 'vip',
            'outstanding_balance' => 0.00,
            'loyalty_points' => 285,
        ]);

        // Additional VIP Customer (Female)
        $ladyCustomer = Customer::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchDowntown->id,
            'customer_code' => 'CUST-VIP-002',
            'first_name' => 'Farhana',
            'last_name' => 'Chowdhury',
            'email' => 'farhana.chowdhury@example.com',
            'phone' => '+880 1711 554433',
            'gender' => 'female',
            'date_of_birth' => '1992-09-21',
            'customer_type' => 'vip',
            'outstanding_balance' => 392.50,
            'loyalty_points' => 195,
        ]);

        // 7. Standard Measurement Templates
        $suitTemplate = MeasurementTemplate::create([
            'tenant_id' => $tenant->id,
            'name' => "Men's Bespoke Two-Piece Suit",
            'gender' => 'male',
            'garment_category' => 'suit',
            'unit' => 'inch',
            'fields' => [
                ['key' => 'chest', 'label' => 'Chest Circumference', 'default_value' => 40],
                ['key' => 'waist', 'label' => 'Trouser Waist', 'default_value' => 34],
                ['key' => 'shoulder', 'label' => 'Shoulder Width', 'default_value' => 18.5],
                ['key' => 'sleeve_length', 'label' => 'Sleeve Length', 'default_value' => 25],
                ['key' => 'jacket_length', 'label' => 'Jacket Length', 'default_value' => 30],
                ['key' => 'inseam', 'label' => 'Trouser Inseam', 'default_value' => 32],
                ['key' => 'crotch_depth', 'label' => 'Crotch / Rise', 'default_value' => 10.5],
                ['key' => 'bicep', 'label' => 'Bicep Girth', 'default_value' => 14.5],
            ],
            'is_active' => true,
        ]);

        $kameezTemplate = MeasurementTemplate::create([
            'tenant_id' => $tenant->id,
            'name' => "Women's Three-Piece Designer Kameez",
            'gender' => 'female',
            'garment_category' => 'dress',
            'unit' => 'inch',
            'fields' => [
                ['key' => 'bust', 'label' => 'Bust Circumference', 'default_value' => 36],
                ['key' => 'waist', 'label' => 'Waist Curve', 'default_value' => 30],
                ['key' => 'hip', 'label' => 'High Hip Circumference', 'default_value' => 40],
                ['key' => 'kameez_length', 'label' => 'Kameez Length', 'default_value' => 44],
                ['key' => 'shoulder', 'label' => 'Across Shoulder', 'default_value' => 14.5],
                ['key' => 'sleeve_length', 'label' => 'Sleeve Length', 'default_value' => 19],
                ['key' => 'trouser_length', 'label' => 'Salwar/Trouser Length', 'default_value' => 38],
            ],
            'is_active' => true,
        ]);

        // 8. Measurement Profiles
        $julianSuitProfile = MeasurementProfile::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $vipCustomer->id,
            'template_id' => $suitTemplate->id,
            'profile_name' => 'Savile Row Slim Fit (Julian)',
            'unit' => 'inch',
            'values' => [
                'chest' => 41.5,
                'waist' => 34.0,
                'shoulder' => 18.75,
                'sleeve_length' => 25.5,
                'jacket_length' => 30.5,
                'inseam' => 32.5,
                'crotch_depth' => 10.75,
                'bicep' => 14.75,
            ],
            'fitting_notes' => 'Erect posture, slight right shoulder drop (0.5 inch). Peaked lapels.',
            'is_default' => true,
            'measured_by' => $tailor->id,
        ]);

        $farhanaKameezProfile = MeasurementProfile::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $ladyCustomer->id,
            'template_id' => $kameezTemplate->id,
            'profile_name' => 'Bridal Festivity Cut',
            'unit' => 'inch',
            'values' => [
                'bust' => 36.5,
                'waist' => 29.5,
                'hip' => 40.5,
                'kameez_length' => 46.0,
                'shoulder' => 14.5,
                'sleeve_length' => 21.0,
                'trouser_length' => 39.0,
            ],
            'fitting_notes' => 'Hourglass poise with sweetheart neck design.',
            'is_default' => true,
            'measured_by' => $tailor->id,
        ]);

        // 9. Garment Types
        $garmentSuit = GarmentType::create([
            'tenant_id' => $tenant->id,
            'name' => 'Two-Piece Bespoke Wool Suit',
            'slug' => 'two-piece-bespoke-wool-suit',
            'gender' => 'men',
            'base_price' => 1200.00,
            'estimated_days' => 14,
            'measurement_template_id' => $suitTemplate->id,
            'is_active' => true,
        ]);

        $garmentKameez = GarmentType::create([
            'tenant_id' => $tenant->id,
            'name' => 'Hand-Embroidered Silk Kameez Set',
            'slug' => 'hand-embroidered-silk-kameez-set',
            'gender' => 'women',
            'base_price' => 850.00,
            'estimated_days' => 10,
            'measurement_template_id' => $kameezTemplate->id,
            'is_active' => true,
        ]);

        // 10. Suppliers & Fabric Inventory
        $supplier = Supplier::create([
            'tenant_id' => $tenant->id,
            'name' => 'Vitale Barberis Canonico Direct',
            'code' => 'SUPP-VBC',
            'contact_person' => 'Marco Rossi',
            'email' => 'sales@vbc-textiles.it',
            'phone' => '+39 015 76491',
            'address' => 'Via Di Pratrivero 10, Biella, Italy',
            'is_active' => true,
        ]);

        $woolFabric = FabricType::create([
            'tenant_id' => $tenant->id,
            'name' => 'Italian Super 150s Midnight Navy Wool',
            'code' => 'FAB-WOOL-150',
            'material_type' => 'wool',
            'color' => 'Midnight Navy',
            'pattern' => 'Herringbone',
            'width' => '60 inch',
            'unit' => 'meter',
            'cost_per_unit' => 65.00,
            'selling_price_per_unit' => 120.00,
            'current_stock' => 42.5,
            'reorder_level' => 15.0,
            'is_active' => true,
        ]);

        $silkFabric = FabricType::create([
            'tenant_id' => $tenant->id,
            'name' => 'Crimson Mulberry Raw Silk',
            'code' => 'FAB-SILK-CRIMSON',
            'material_type' => 'silk',
            'color' => 'Crimson Gold',
            'pattern' => 'Zari Jacquard',
            'width' => '45 inch',
            'unit' => 'meter',
            'cost_per_unit' => 45.00,
            'selling_price_per_unit' => 95.00,
            'current_stock' => 28.0,
            'reorder_level' => 10.0,
            'is_active' => true,
        ]);

        $woolRoll = FabricRoll::create([
            'tenant_id' => $tenant->id,
            'fabric_type_id' => $woolFabric->id,
            'roll_number' => 'VBC-ROLL-4091',
            'batch_number' => 'BATCH-2026-AUTUMN',
            'initial_quantity' => 50.0,
            'current_quantity' => 42.5,
            'unit' => 'meter',
            'cost_price' => 65.00,
            'status' => 'in_stock',
        ]);

        $silkRoll = FabricRoll::create([
            'tenant_id' => $tenant->id,
            'fabric_type_id' => $silkFabric->id,
            'roll_number' => 'SILK-ROLL-8821',
            'batch_number' => 'BATCH-2026-FESTIVE',
            'initial_quantity' => 35.0,
            'current_quantity' => 28.0,
            'unit' => 'meter',
            'cost_price' => 45.00,
            'status' => 'in_stock',
        ]);

        $buttons = InventoryItem::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'name' => 'Genuine Horn Buttons 4-Hole (Set of 12)',
            'sku' => 'HAB-HORN-01',
            'category' => 'buttons',
            'unit' => 'piece',
            'cost_price' => 8.50,
            'current_stock' => 120,
            'reorder_level' => 30,
            'is_active' => true,
        ]);

        // 11. Orders across Workflow Stages
        // Order 1: Delivered Bespoke Suit
        $order1 = Order::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'customer_id' => $vipCustomer->id,
            'order_number' => 'ORD-202610-001',
            'order_type' => 'custom_tailoring',
            'status' => 'delivered',
            'order_date' => now()->subWeeks(3)->toDateString(),
            'expected_delivery_date' => now()->subDays(2)->toDateString(),
            'actual_delivery_date' => now()->subDay()->toDateString(),
            'total_material_cost' => 227.50,
            'total_labor_cost' => 350.00,
            'additional_charges' => 0.00,
            'discount_amount' => 50.00,
            'tax_rate' => 5.0,
            'tax_amount' => 60.00,
            'total_amount' => 1210.00,
            'paid_amount' => 1210.00,
            'balance_amount' => 0.00,
            'payment_status' => 'paid',
            'assigned_tailor_id' => $tailor->id,
            'priority' => 'normal',
            'notes' => 'Delivered to client private residence in Gulshan.',
            'created_by' => $owner->id,
        ]);

        $order1Item = OrderItem::create([
            'order_id' => $order1->id,
            'garment_type_id' => $garmentSuit->id,
            'fabric_type_id' => $woolFabric->id,
            'measurement_profile_id' => $julianSuitProfile->id,
            'item_name' => 'Bespoke Midnight Navy 2-Piece Suit',
            'quantity' => 1,
            'unit_price' => 1200.00,
            'material_cost' => 227.50,
            'labor_cost' => 350.00,
            'total_price' => 1200.00,
            'measurement_snapshot' => [
                'profile_id' => $julianSuitProfile->id,
                'profile_name' => $julianSuitProfile->profile_name,
                'values' => $julianSuitProfile->values,
                'frozen_at' => now()->subWeeks(3)->toIso8601String(),
            ],
            'special_instructions' => 'Hand-stitched lapel Milanese buttonhole, horn buttons, dual vents.',
            'status' => 'completed',
        ]);

        $order1Job = JobCard::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order1->id,
            'order_item_id' => $order1Item->id,
            'job_number' => 'JOB-202610-001',
            'stage' => 'finishing',
            'assigned_to' => $tailor->id,
            'department' => 'tailoring',
            'priority' => 'normal',
            'estimated_hours' => 32.0,
            'actual_hours' => 30.5,
            'started_at' => now()->subWeeks(3),
            'completed_at' => now()->subDays(4),
            'status' => 'completed',
            'instructions' => 'Roped shoulders, 3.5 inch notch lapel.',
            'tailor_notes' => 'Finished with premium lining and hand-stitched cuffs.',
        ]);

        Delivery::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order1->id,
            'delivery_number' => 'DEL-202610-001',
            'delivery_type' => 'courier',
            'status' => 'delivered',
            'recipient_name' => 'Julian Montgomery',
            'recipient_phone' => '+880 1711 998877',
            'delivery_address' => 'House 42, Road 7, Gulshan-2, Dhaka',
            'scheduled_date' => now()->subDays(2)->toDateString(),
            'delivered_at' => now()->subDay(),
            'tracking_number' => 'EXP-GULSHAN-771',
            'notes' => 'Received and inspected in person.',
        ]);

        $order1Invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'order_id' => $order1->id,
            'customer_id' => $vipCustomer->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => now()->subWeeks(3)->toDateString(),
            'due_date' => now()->subWeeks(2)->toDateString(),
            'subtotal' => 1200.00,
            'discount_amount' => 50.00,
            'tax_rate' => 5.0,
            'tax_amount' => 60.00,
            'total_amount' => 1210.00,
            'paid_amount' => 1210.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $accountant->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $order1Invoice->id,
            'description' => 'Bespoke Midnight Navy 2-Piece Suit',
            'quantity' => 1,
            'unit_price' => 1200.00,
            'total_price' => 1200.00,
        ]);

        Payment::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'invoice_id' => $order1Invoice->id,
            'order_id' => $order1->id,
            'customer_id' => $vipCustomer->id,
            'payment_number' => 'PAY-2026-0001',
            'amount' => 1210.00,
            'payment_method' => 'card',
            'payment_date' => now()->subWeeks(3)->toDateString(),
            'transaction_reference' => 'TXN-VISA-99412',
            'notes' => 'Settled in full via Visa Platinum POS terminal',
            'recorded_by' => $accountant->id,
        ]);

        // Order 2: In Stitching / Production with Partial Payment
        $order2 = Order::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchDowntown->id,
            'customer_id' => $ladyCustomer->id,
            'order_number' => 'ORD-202611-002',
            'order_type' => 'custom_tailoring',
            'status' => 'stitching',
            'order_date' => now()->subDays(4)->toDateString(),
            'expected_delivery_date' => now()->addDays(5)->toDateString(),
            'total_material_cost' => 202.50,
            'total_labor_cost' => 250.00,
            'additional_charges' => 0.00,
            'discount_amount' => 0.00,
            'tax_rate' => 5.0,
            'tax_amount' => 42.50,
            'total_amount' => 892.50,
            'paid_amount' => 500.00,
            'balance_amount' => 392.50,
            'payment_status' => 'partial',
            'assigned_tailor_id' => $tailor->id,
            'priority' => 'urgent',
            'notes' => 'Bridal festive event on weekend. Rush production priority.',
            'created_by' => $manager->id,
        ]);

        $order2Item = OrderItem::create([
            'order_id' => $order2->id,
            'garment_type_id' => $garmentKameez->id,
            'fabric_type_id' => $silkFabric->id,
            'measurement_profile_id' => $farhanaKameezProfile->id,
            'item_name' => 'Royal Crimson Zari Silk Kameez Set',
            'quantity' => 1,
            'unit_price' => 850.00,
            'material_cost' => 202.50,
            'labor_cost' => 250.00,
            'total_price' => 850.00,
            'measurement_snapshot' => [
                'profile_id' => $farhanaKameezProfile->id,
                'profile_name' => $farhanaKameezProfile->profile_name,
                'values' => $farhanaKameezProfile->values,
                'frozen_at' => now()->subDays(4)->toIso8601String(),
            ],
            'special_instructions' => 'Heavy gold beadwork along neckline and sleeves.',
            'status' => 'in_progress',
        ]);

        JobCard::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order2->id,
            'order_item_id' => $order2Item->id,
            'job_number' => 'JOB-202611-002',
            'stage' => 'stitching',
            'assigned_to' => $tailor->id,
            'department' => 'tailoring',
            'priority' => 'urgent',
            'estimated_hours' => 24.0,
            'actual_hours' => 14.0,
            'started_at' => now()->subDays(3),
            'status' => 'in_progress',
            'instructions' => 'A-line flare with sweetheart neck and side slit facing.',
        ]);

        $order2Invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchDowntown->id,
            'order_id' => $order2->id,
            'customer_id' => $ladyCustomer->id,
            'invoice_number' => 'INV-2026-0002',
            'invoice_date' => now()->subDays(4)->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'subtotal' => 850.00,
            'discount_amount' => 0.00,
            'tax_rate' => 5.0,
            'tax_amount' => 42.50,
            'total_amount' => 892.50,
            'paid_amount' => 500.00,
            'balance_amount' => 392.50,
            'status' => 'partially_paid',
            'created_by' => $accountant->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $order2Invoice->id,
            'description' => 'Royal Crimson Zari Silk Kameez Set',
            'quantity' => 1,
            'unit_price' => 850.00,
            'total_price' => 850.00,
        ]);

        Payment::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchDowntown->id,
            'invoice_id' => $order2Invoice->id,
            'order_id' => $order2->id,
            'customer_id' => $ladyCustomer->id,
            'payment_number' => 'PAY-2026-0002',
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now()->subDays(4)->toDateString(),
            'transaction_reference' => 'CASH-DEP-500',
            'is_advance' => true,
            'notes' => '55% Advance booking deposit paid at Banani Studio',
            'recorded_by' => $receptionist->id,
        ]);

        QualityInspection::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order1->id,
            'order_item_id' => $order1Item->id,
            'inspector_id' => $manager->id,
            'inspection_date' => now()->subDays(3)->toDateString(),
            'status' => 'passed',
            'checklist_results' => [
                'stitching' => 'excellent',
                'measurements' => 'exact_match',
                'fabric_quality' => 'verified_flawless',
                'finishing' => 'steamed_pressed',
            ],
            'action_taken' => 'Passed final inspection with zero defects; steaming and tagging approved.',
        ]);

        Fitting::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order2->id,
            'customer_id' => $ladyCustomer->id,
            'round_number' => 1,
            'fitting_date' => now()->addDays(2)->setTime(16, 0),
            'fitter_id' => $tailor->id,
            'status' => 'scheduled',
            'adjustment_instructions' => 'Trial fit for sleeve pitch and waist drape before final beadwork attachment.',
        ]);

        // 12. Expenses & Cashbook
        $rentCategory = ExpenseCategory::create([
            'tenant_id' => $tenant->id,
            'name' => 'Atelier Studio Rent & Utilities',
            'code' => 'EXP-RENT',
            'is_active' => true,
        ]);

        $haberdasheryCategory = ExpenseCategory::create([
            'tenant_id' => $tenant->id,
            'name' => 'Sewing Thread & Consumables',
            'code' => 'EXP-SUPPLY',
            'is_active' => true,
        ]);

        Expense::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'expense_category_id' => $rentCategory->id,
            'title' => 'Gulshan Atelier Monthly Facility Lease',
            'amount' => 1500.00,
            'expense_date' => now()->startOfMonth()->toDateString(),
            'payment_method' => 'bank_transfer',
            'notes' => 'Commercial floor rent for Gulshan-2 boutique showroom and workshop.',
            'recorded_by' => $owner->id,
        ]);

        Expense::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'expense_category_id' => $haberdasheryCategory->id,
            'title' => 'Gütermann Silk Thread & Organza Interlining',
            'amount' => 120.00,
            'expense_date' => now()->subDays(5)->toDateString(),
            'payment_method' => 'cash',
            'notes' => 'German imported sewing threads and canvas interlinings.',
            'recorded_by' => $storekeeper->id,
        ]);

        // 13. HR Work Shifts & Biometric Attendance
        $morningShift = Shift::create([
            'tenant_id' => $tenant->id,
            'name' => 'Morning Atelier Production Shift',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'late_threshold_minutes' => 15,
            'is_active' => true,
        ]);

        EmployeeAttendance::create([
            'tenant_id' => $tenant->id,
            'user_id' => $tailor->id,
            'branch_id' => $branchMain->id,
            'shift_id' => $morningShift->id,
            'attendance_date' => now()->toDateString(),
            'check_in' => '08:55:00',
            'check_out' => null,
            'status' => 'present',
            'working_hours' => 4.5,
            'overtime_hours' => 0.0,
            'source' => 'biometric_device',
        ]);

        EmployeeAttendance::create([
            'tenant_id' => $tenant->id,
            'user_id' => $receptionist->id,
            'branch_id' => $branchMain->id,
            'shift_id' => $morningShift->id,
            'attendance_date' => now()->toDateString(),
            'check_in' => '09:00:00',
            'check_out' => null,
            'status' => 'present',
            'working_hours' => 4.4,
            'overtime_hours' => 0.0,
            'source' => 'biometric_device',
        ]);
    }
}

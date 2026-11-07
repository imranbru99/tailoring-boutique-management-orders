<?php

namespace App\Http\Controllers;

use App\Core\Modules\ModuleManager;
use App\Core\Modules\ModuleRegistry;
use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OnboardingWizardController extends Controller
{
    public function show()
    {
        $modules = ModuleRegistry::all();
        return view('onboarding.wizard', compact('modules'));
    }

    public function process(Request $request, ModuleManager $moduleManager)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:100',
            'business_type' => 'required|string|in:tailoring_shop,boutique,fashion_studio,alteration_shop,bridal_studio,uniform_manufacturer',
            'currency' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:5',
            'measurement_unit' => 'required|in:inch,cm',
            'timezone' => 'required|string',
            'owner_name' => 'required|string|max:100',
            'owner_email' => 'required|email',
            'owner_password' => 'required|min:8',
            'branch_name' => 'required|string|max:100',
            'selected_modules' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($validated, $moduleManager) {
            $slug = Str::slug($validated['business_name']) . '-' . rand(100, 999);

            // 1. Create Tenant
            $tenant = Tenant::create([
                'name' => $validated['business_name'],
                'slug' => $slug,
                'business_type' => $validated['business_type'],
                'currency' => $validated['currency'],
                'currency_symbol' => $validated['currency_symbol'],
                'measurement_unit' => $validated['measurement_unit'],
                'timezone' => $validated['timezone'],
                'mode' => 'multi_branch',
                'is_active' => true,
            ]);

            // 2. Create Branch
            $branch = Branch::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['branch_name'],
                'code' => 'MAIN',
                'is_main' => true,
                'is_active' => true,
            ]);

            // 3. Create or Link Owner User
            $owner = User::firstOrNew(['email' => $validated['owner_email']]);
            $owner->name = $validated['owner_name'];
            $owner->password = Hash::make($validated['owner_password']);
            $owner->tenant_id = $tenant->id;
            $owner->current_branch_id = $branch->id;
            $owner->role = 'business_owner';
            $owner->job_title = 'Owner & Creative Director';
            $owner->is_active = true;
            $owner->save();

            $owner->branches()->syncWithoutDetaching([$branch->id => ['is_primary' => true]]);

            // 4. Initialize Recommended Modules
            $moduleManager->syncDefaultModules($tenant->id, $validated['business_type']);

            // If user customized specific modules, apply them
            if (!empty($validated['selected_modules'])) {
                foreach ($validated['selected_modules'] as $modKey) {
                    $moduleManager->enable($modKey, $tenant->id);
                }
            }

            auth()->login($owner);

            return redirect("/app/{$tenant->slug}")->with('success', "Welcome to {$tenant->name}! Your atelier management OS is ready.");
        });
    }
}

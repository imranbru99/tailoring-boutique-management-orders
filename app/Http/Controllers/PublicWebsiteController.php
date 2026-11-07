<?php

namespace App\Http\Controllers;

use App\Core\Modules\ModuleRegistry;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicWebsiteController extends Controller
{
    public function index()
    {
        $modules = ModuleRegistry::all();
        $totalBusinesses = Tenant::count();
        $totalOrders = Order::count();

        return view('public.home', compact('modules', 'totalBusinesses', 'totalOrders'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function modules()
    {
        $modules = ModuleRegistry::all();
        return view('public.modules', compact('modules'));
    }

    public function moduleDetail(string $key)
    {
        $module = ModuleRegistry::get($key);
        if (!$module) {
            abort(404, 'Module not found in registry.');
        }

        $allModules = ModuleRegistry::all();
        return view('public.module-detail', compact('module', 'allModules'));
    }

    public function solution(string $type)
    {
        $validTypes = ['tailoring', 'boutique', 'studio', 'alteration'];
        if (!in_array($type, $validTypes, true)) {
            abort(404);
        }

        $modules = ModuleRegistry::all();
        return view('public.solution', compact('type', 'modules'));
    }

    public function pricing()
    {
        $modules = ModuleRegistry::all();
        return view('public.pricing', compact('modules'));
    }

    public function demo()
    {
        $demoUsers = [
            [
                'role' => 'Platform Super Admin',
                'email' => 'platform-admin@tailor.test',
                'description' => 'Platform-wide control center: manage all tenants, SaaS subscription plans, global modules.',
                'panel' => '/admin',
            ],
            [
                'role' => 'Business Owner',
                'email' => 'owner@tailor.test',
                'description' => 'Complete business operating system: executive dashboard, module activation, financial cashbook.',
                'panel' => '/app',
            ],
            [
                'role' => 'Branch Manager',
                'email' => 'manager@tailor.test',
                'description' => 'Branch supervision: staff attendance, customer appointments, inventory transfers.',
                'panel' => '/app',
            ],
            [
                'role' => 'Master Tailor',
                'email' => 'tailor@tailor.test',
                'description' => 'Workshop production: cutting & stitching job cards, measurement specification cards.',
                'panel' => '/app',
            ],
            [
                'role' => 'Receptionist',
                'email' => 'reception@tailor.test',
                'description' => 'Front desk: customer registration, appointment booking, pickup & delivery handovers.',
                'panel' => '/app',
            ],
            [
                'role' => 'Inventory Manager',
                'email' => 'inventory@tailor.test',
                'description' => 'Materials & Haberdashery: fabric roll batch tracking, reorder warnings, supplier orders.',
                'panel' => '/app',
            ],
            [
                'role' => 'Accountant',
                'email' => 'accountant@tailor.test',
                'description' => 'Financial accounting: invoices, split payments, expense recording, cashbook reconciliation.',
                'panel' => '/app',
            ],
            [
                'role' => 'Customer Client',
                'email' => 'customer@tailor.test',
                'description' => 'Self-service portal: view tailoring progress, download measurement sheets, inspect invoices.',
                'panel' => '/portal',
            ],
        ];

        return view('public.demo', compact('demoUsers'));
    }

    public function loginAsDemoUser(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return back()->with('error', 'Demo user not found. Please run database seeders.');
        }

        Auth::login($user);

        if ($user->role === 'super_admin') {
            return redirect('/admin');
        }

        if ($user->role === 'customer') {
            return redirect('/portal');
        }

        $tenant = $user->tenant ?? Tenant::first();
        return redirect("/app/{$tenant->slug}");
    }

    public function docs()
    {
        $modules = ModuleRegistry::all();
        return view('public.docs', compact('modules'));
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function faq()
    {
        return view('public.faq');
    }

    public function terms()
    {
        return view('public.terms');
    }

    public function privacy()
    {
        return view('public.privacy');
    }
}

@extends('public.layout')

@section('title', 'Documentation & Knowledge Base')

@section('content')
<div class="py-16 bg-slate-900 border-b border-slate-800 text-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
            Developer & Operator Manual
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight font-serif">Platform Documentation</h1>
        <p class="mt-2 text-lg text-slate-300">
            Comprehensive guides for configuring modules, measurement engines, multi-tenancy, POS integration, and REST APIs.
        </p>
    </div>
</div>

<div class="py-16 bg-slate-950 text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <!-- Table of Contents -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-4 text-sm">
                    <h3 class="text-white font-bold uppercase tracking-wider text-xs">Getting Started</h3>
                    <ul class="space-y-2 border-l border-slate-800 pl-4">
                        <li><a href="#quickstart" class="hover:text-emerald-400 transition">System Quickstart</a></li>
                        <li><a href="#tenancy" class="hover:text-emerald-400 transition">Multi-Tenancy Architecture</a></li>
                        <li><a href="#modules" class="hover:text-emerald-400 transition">Dynamic Module Engine</a></li>
                        <li><a href="#measurements" class="hover:text-emerald-400 transition">Measurement Snapshots</a></li>
                        <li><a href="#production" class="hover:text-emerald-400 transition">18-Stage Production Flow</a></li>
                        <li><a href="#attendance" class="hover:text-emerald-400 transition">Biometric Attendance Hub</a></li>
                        <li><a href="#pdf-engine" class="hover:text-emerald-400 transition">Unicode PDF Generation</a></li>
                        <li><a href="#api" class="hover:text-emerald-400 transition">REST API & Tokens</a></li>
                    </ul>

                    <div class="pt-6 border-t border-slate-800">
                        <a href="{{ route('demo') }}" class="block text-center py-2 px-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold hover:bg-emerald-500 hover:text-slate-950 transition text-xs">
                            Launch Live Demo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="lg:col-span-3 space-y-16">
                <!-- Quickstart -->
                <section id="quickstart">
                    <h2 class="text-2xl font-bold text-white font-serif mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">1</span>
                        System Quickstart
                    </h2>
                    <p class="text-slate-400 leading-relaxed">
                        The platform operates in either standalone boutique mode or multi-tenant SaaS mode. Upon initial deployment or onboarding, an initial tenant organization is initialized with default branches, base measurement templates (Men's Suit, Women's Kameez, Evening Gowns), and currency settings.
                    </p>
                    <div class="mt-4 p-4 rounded-xl bg-slate-900 border border-slate-800 font-mono text-xs text-emerald-300">
                        <code>
                            # 1. Clone repository and install dependencies<br>
                            composer install && npm install && npm run build<br><br>
                            # 2. Run migrations and seed demo personas<br>
                            php artisan migrate --seed<br><br>
                            # 3. Access admin panel or boutique OS panel<br>
                            http://127.0.0.1:8000/app/elegant-stitch-boutique
                        </code>
                    </div>
                </section>

                <!-- Tenancy -->
                <section id="tenancy">
                    <h2 class="text-2xl font-bold text-white font-serif mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">2</span>
                        Multi-Tenancy & Data Isolation
                    </h2>
                    <p class="text-slate-400 leading-relaxed">
                        Tenancy is enforced transparently using the <code class="text-emerald-400">BelongsToTenant</code> Eloquent trait paired with the global query scope <code class="text-emerald-400">TenantScope</code>. Every database query automatically restricts rows by the active tenant ID, preventing cross-tenant leakage.
                    </p>
                    <div class="mt-4 p-5 rounded-2xl bg-slate-900/60 border border-slate-800 text-sm space-y-3">
                        <div class="font-bold text-white">Branch-Level Partitioning</div>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            A single boutique tenant can register multiple geographical branches (e.g., Gulshan Main Atelier and Banani Downtown Studio). Users, inventory, orders, and POS cash registers can be scoped to specific branches or set as tenant-wide.
                        </p>
                    </div>
                </section>

                <!-- Modules -->
                <section id="modules">
                    <h2 class="text-2xl font-bold text-white font-serif mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">3</span>
                        Dynamic Module Engine
                    </h2>
                    <p class="text-slate-400 leading-relaxed">
                        30 independent modules constitute the business core. Each module is registered in <code class="text-emerald-400">App\Core\Modules\ModuleRegistry</code> and managed via <code class="text-emerald-400">ModuleManager</code>.
                    </p>
                    <ul class="mt-4 list-disc list-inside space-y-2 text-sm text-slate-400">
                        <li><strong class="text-white">Core Protection:</strong> Fundamental modules like Customers, Orders, and Security cannot be disabled.</li>
                        <li><strong class="text-white">Dependency Graphs:</strong> Deactivating a parent module (e.g. Orders) will validate and warn before allowing dependent modules (Job Cards, Alterations) to be modified.</li>
                        <li><strong class="text-white">Zero Data Deletion:</strong> Disabling a module only conceals its UI navigation, dashboard widgets, and route access; past records remain safe.</li>
                    </ul>
                </section>

                <!-- Measurements -->
                <section id="measurements">
                    <h2 class="text-2xl font-bold text-white font-serif mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">4</span>
                        Measurement Engine & Immutable Snapshots
                    </h2>
                    <p class="text-slate-400 leading-relaxed">
                        Precision tailoring requires rigorous measurement tracking. Customers may maintain multiple measurement profiles (e.g., Slim Fit Shirt, Traditional Sherwani, Loose Abaya). When an order is confirmed, the specific profile measurements are frozen in JSON snapshot format directly inside the <code class="text-emerald-400">order_items</code> table.
                    </p>
                </section>

                <!-- Production -->
                <section id="production">
                    <h2 class="text-2xl font-bold text-white font-serif mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">5</span>
                        18-Stage Production Flow & Job Cards
                    </h2>
                    <p class="text-slate-400 leading-relaxed">
                        Orders progress through structured, audited stages:
                    </p>
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs font-mono">
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">1. draft</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">2. confirmed</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">3. fabric_sourced</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">4. fabric_cut</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">5. pattern_making</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">6. in_cutting</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">7. cut_completed</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">8. assigned_tailor</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">9. in_stitching</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">10. stitching_done</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">11. embroidery</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">12. finishing</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">13. quality_check</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">14. fitting_scheduled</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">15. alteration</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">16. ready_delivery</div>
                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">17. out_delivery</div>
                        <div class="p-2 rounded bg-emerald-950 border border-emerald-700 text-emerald-400 font-bold">18. delivered</div>
                    </div>
                </section>

                <!-- API -->
                <section id="api">
                    <h2 class="text-2xl font-bold text-white font-serif mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">6</span>
                        REST API & Integrations
                    </h2>
                    <p class="text-slate-400 leading-relaxed">
                        External client apps, eCommerce storefronts, and mobile scanner tablets authenticate using Laravel Sanctum bearer tokens. Endpoints are versioned under <code class="text-emerald-400">/api/v1/</code>.
                    </p>
                    <div class="mt-4 p-4 rounded-xl bg-slate-900 border border-slate-800 font-mono text-xs text-slate-300 space-y-2">
                        <div>POST /api/v1/auth/login <span class="text-slate-500">— Obtain Bearer token</span></div>
                        <div>GET /api/v1/customers <span class="text-slate-500">— Paginated customer records</span></div>
                        <div>POST /api/v1/orders <span class="text-slate-500">— Create custom tailoring order</span></div>
                        <div>GET /api/v1/orders/{id}/job-card <span class="text-slate-500">— Printable job card data</span></div>
                        <div>POST /api/v1/attendance/punch <span class="text-slate-500">— Biometric terminal clock event</span></div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection

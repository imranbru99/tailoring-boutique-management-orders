@extends('public.layout')

@section('title', 'About Us — Precision Tailoring & Boutique Operating System')

@section('content')
<div class="py-20 bg-slate-900 border-b border-slate-800 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-radial-gradient from-emerald-950/30 to-slate-950/90 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-4">
            Our Craft & Vision
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight font-serif">
            Empowering Modern Sartorial Excellence
        </h1>
        <p class="mt-6 text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
            Bridging timeless bespoke craftsmanship with enterprise-grade multi-tenant software, AI-driven pattern intelligence, and end-to-end atelier workflows.
        </p>
    </div>
</div>

<div class="py-20 bg-slate-950 text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-3xl font-bold text-white font-serif tracking-tight">The Modern Atelier Operating System</h2>
                <p class="mt-6 text-slate-400 leading-relaxed">
                    Custom garment tailoring is an art of extreme precision. Millimeters dictate posture, drape, and poise. Yet worldwide, master tailors, bespoke boutiques, and couture fashion houses have historically wrestled with fragmented spreadsheets, paper notebooks, lost measurement chits, and missed delivery deadlines.
                </p>
                <p class="mt-4 text-slate-400 leading-relaxed">
                    Our platform was architected from the ground up to digitize every step of the garment journey: from the initial customer tape measurement and 3D posture profile to fabric roll inventory reservation, 18-stage job card routing, multi-stage quality control, fitting revisions, and scheduled white-glove delivery.
                </p>
                <div class="mt-8 grid grid-cols-2 gap-6">
                    <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
                        <div class="text-3xl font-bold text-emerald-400 font-mono">100%</div>
                        <div class="mt-1 text-sm text-slate-400">Measurement Snapshot Immortality</div>
                    </div>
                    <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
                        <div class="text-3xl font-bold text-amber-400 font-mono">30+</div>
                        <div class="mt-1 text-sm text-slate-400">Modular Business Subsystems</div>
                    </div>
                    <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
                        <div class="text-3xl font-bold text-sky-400 font-mono">Multi-Tenant</div>
                        <div class="mt-1 text-sm text-slate-400">Isolated Cloud or Single Studio</div>
                    </div>
                    <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
                        <div class="text-3xl font-bold text-purple-400 font-mono">Native AI</div>
                        <div class="mt-1 text-sm text-slate-400">Fabric Catalog & Style Advisor</div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-2xl relative overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-6">
                        <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                        <span class="text-xs text-slate-400 font-mono">core_architecture.manifest</span>
                    </div>
                    <pre class="text-xs text-emerald-300 font-mono leading-relaxed overflow-x-auto"><code>[Atelier Core OS]
├── Tenancy: Multi-Tenant & Multi-Branch Scoped
├── Module Engine: 30 Decoupled Swappable Modules
├── Precision: Fractional & Metric Measurement Engine
├── Workflow: 18 Discrete Order Production Stages
├── Job Cards: Automated Cutting & Tailoring Routing
├── Hardware: Biometric Attendance & Thermal POS
├── AI Layer: Provider-Agnostic Model Hub (Gemini/GPT/Claude)
└── Documents: Multilingual Unicode PDF Engine (RTL/Indic)</code></pre>
                </div>
            </div>
        </div>

        <div class="mt-28">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-white font-serif">Core Engineering Principles</h2>
                <p class="mt-3 text-slate-400">Every architectural decision was made to ensure uncompromised business continuity, customer trust, and operational speed.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/40 transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-2xl mb-6">🔒</div>
                    <h3 class="text-xl font-bold text-white mb-2">Immutable Measurement Snapshots</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        When an order is created, the client's current body dimensions are deeply cloned directly into the order record. Even if the customer profile is updated 6 months later, the original cutting specifications are preserved untouched forever.
                    </p>
                </div>

                <div class="p-8 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-amber-500/40 transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-2xl mb-6">⚙️</div>
                    <h3 class="text-xl font-bold text-white mb-2">Zero-Bloat Modular Architecture</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        A boutique owner who only crafts evening dresses can deactivate the alteration and attendance modules with one click. Inactive modules vanish cleanly from navigation, routes, and widgets without deleting historical records.
                    </p>
                </div>

                <div class="p-8 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-purple-500/40 transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-2xl mb-6">🌍</div>
                    <h3 class="text-xl font-bold text-white mb-2">Universal Multilingual Typography</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Invoices, delivery chits, and production job cards render natively in English, Bengali, Hindi, Arabic, Urdu, and complex scripts with perfect font shaping, RTL orientation, and barcode tracking.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-28 p-10 rounded-3xl bg-gradient-to-r from-emerald-950/40 to-slate-900 border border-emerald-500/30 text-center">
            <h2 class="text-3xl font-bold text-white font-serif">Ready to modernise your boutique?</h2>
            <p class="mt-3 text-slate-300 max-w-2xl mx-auto">
                Explore our live interactive demo with 8 role personas or create your bespoke tenant workspace in under 60 seconds.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('demo') }}" class="px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold transition">
                    Launch Interactive Demo
                </a>
                <a href="{{ route('onboarding') }}" class="px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold border border-slate-700 transition">
                    Start Guided Setup
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

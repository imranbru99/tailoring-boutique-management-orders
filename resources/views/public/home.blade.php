@extends('public.layout')

@section('title', 'Tailoring & Boutique Management | Commercial SaaS Operating System')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-12 pb-24 lg:pt-20 lg:pb-32">
    <!-- Subtle gradient glow -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-tr from-emerald-500/20 via-teal-500/10 to-indigo-500/20 blur-[140px] pointer-events-none rounded-full"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900 border border-slate-800 text-xs text-slate-300 font-semibold mb-6 shadow-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>The Complete Operating System for Bespoke Tailoring & Fashion Studios</span>
            </div>
            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-[1.1] mb-6">
                From Anatomical Measurements to <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-indigo-400">Flawless Bespoke Delivery</span>.
            </h1>
            <p class="text-slate-400 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto">
                Replace fragmented notebooks and generic spreadsheets. Elevate your atelier with custom measurement snapshots, workshop job cards, fabric roll batch tracking, interactive POS, and automated client notifications.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/onboarding" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-950 font-bold rounded-2xl shadow-xl shadow-emerald-500/25 transition-all text-sm flex items-center justify-center gap-2">
                    <span>Start Free 14-Day Trial</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="/demo" class="w-full sm:w-auto px-8 py-4 bg-slate-900 hover:bg-slate-800 border border-slate-700 text-white font-bold rounded-2xl transition-all text-sm flex items-center justify-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Explore Live Interactive Demo</span>
                </a>
            </div>

            <!-- Trust Metrics -->
            <div class="mt-12 pt-8 border-t border-slate-800/80 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="text-2xl font-black text-white">30 Modules</div>
                    <div class="text-xs text-slate-500 mt-1">Dynamic Feature Engine</div>
                </div>
                <div>
                    <div class="text-2xl font-black text-emerald-400">100%</div>
                    <div class="text-xs text-slate-500 mt-1">Snapshot Immobility</div>
                </div>
                <div>
                    <div class="text-2xl font-black text-teal-400">5 Languages</div>
                    <div class="text-xs text-slate-500 mt-1">English, বাংলা, العربية, Hindi, Urdu</div>
                </div>
                <div>
                    <div class="text-2xl font-black text-indigo-400">&lt; 20 ms</div>
                    <div class="text-xs text-slate-500 mt-1">Drutogoti Tuned Latency</div>
                </div>
            </div>
        </div>

        <!-- Product Dashboard Preview Card -->
        <div class="relative mx-auto max-w-5xl rounded-3xl p-3 bg-gradient-to-b from-slate-700/50 via-slate-800/20 to-slate-900 border border-slate-700/60 shadow-2xl shadow-emerald-500/10">
            <div class="bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 p-6 space-y-6">
                <!-- Mock Top bar -->
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-mono text-slate-400 ml-2">app.tailor.test/app/elegant-stitch-boutique</span>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 bg-emerald-950 text-emerald-400 border border-emerald-800/60 rounded-full">
                        Atelier OS v2.0 Active
                    </span>
                </div>

                <!-- Mock Dashboard Widgets -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-slate-950 p-4 rounded-xl border border-slate-800">
                        <span class="text-xs text-slate-500 block">Orders In Production</span>
                        <span class="text-2xl font-black text-amber-400 mt-1 block">14 Garments</span>
                        <span class="text-[11px] text-slate-400 mt-1 block">Cutting &bull; Stitching &bull; Finishing</span>
                    </div>
                    <div class="bg-slate-950 p-4 rounded-xl border border-slate-800">
                        <span class="text-xs text-slate-500 block">Ready For Handover</span>
                        <span class="text-2xl font-black text-emerald-400 mt-1 block">6 Orders</span>
                        <span class="text-[11px] text-slate-400 mt-1 block">QC Passed & Packed</span>
                    </div>
                    <div class="bg-slate-950 p-4 rounded-xl border border-slate-800">
                        <span class="text-xs text-slate-500 block">Month Net Sales</span>
                        <span class="text-2xl font-black text-teal-400 mt-1 block">$18,450.00</span>
                        <span class="text-[11px] text-emerald-400 mt-1 block">&uarr; 24% vs last month</span>
                    </div>
                    <div class="bg-slate-950 p-4 rounded-xl border border-slate-800">
                        <span class="text-xs text-slate-500 block">Fabric Stock Value</span>
                        <span class="text-2xl font-black text-indigo-400 mt-1 block">$24,190.00</span>
                        <span class="text-[11px] text-slate-400 mt-1 block">180 Rolls in Warehouse</span>
                    </div>
                </div>

                <!-- Mock Workflow Progression Bar -->
                <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <span class="font-bold text-white">Live Workflow Pipeline:</span>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300">1. Inquiry</span>
                    <span class="text-slate-600">&rarr;</span>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300">2. Measurement</span>
                    <span class="text-slate-600">&rarr;</span>
                    <span class="px-2.5 py-1 rounded-lg bg-amber-950 text-amber-300 border border-amber-800">3. Cutting</span>
                    <span class="text-slate-600">&rarr;</span>
                    <span class="px-2.5 py-1 rounded-lg bg-orange-950 text-orange-300 border border-orange-800">4. Stitching</span>
                    <span class="text-slate-600">&rarr;</span>
                    <span class="px-2.5 py-1 rounded-lg bg-indigo-950 text-indigo-300 border border-indigo-800">5. Quality QC</span>
                    <span class="text-slate-600">&rarr;</span>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-950 text-emerald-300 border border-emerald-800">6. Delivery</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The Complete Tailoring Workflow Section -->
<section class="py-20 bg-slate-900/60 border-y border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-mono text-emerald-400 font-bold uppercase tracking-wider block mb-2">Built for Real Atelier Operations</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white">How Our Operating System Runs Your Business</h2>
            <p class="text-slate-400 text-sm mt-3">Every stage is interconnected—preventing costly fabric miscalculations, measurement discrepancies, and delayed delivery promises.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-black text-lg">01</div>
                <h3 class="text-lg font-bold text-white">Client & Measurement Snapshot</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Capture anatomical measurements in inches or centimeters using customizable templates (Suits, Sherwanis, Blouses, Gowns). Orders lock an immutable measurement snapshot—future changes to the client card will never corrupt historical orders.
                </p>
            </div>

            <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center font-black text-lg">02</div>
                <h3 class="text-lg font-bold text-white">Production Kanban & Job Cards</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Confirmed orders generate bench job cards for cutting, stitching, embroidery, and hand finishing. Tailors can print or inspect exact measurement specifications and chalk marks with zero room for ambiguity.
                </p>
            </div>

            <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-lg">03</div>
                <h3 class="text-lg font-bold text-white">Quality Inspection & Delivery</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Strict quality audits prevent defective garments from reaching clients. Fitting trials with pin adjustments route directly into alteration assignments. Dispatch slips and receipts confirm final customer handover.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 30 Modules Directory Overview -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <span class="text-xs font-mono text-emerald-400 font-bold uppercase tracking-wider block mb-2">Dynamic Module Engine</span>
                <h2 class="text-3xl sm:text-4xl font-black text-white">All 30 Comprehensive Modules</h2>
                <p class="text-slate-400 text-sm mt-2 max-w-2xl">Every single feature is built as an independent, testable module with dependency enforcement and zero data loss on deactivation.</p>
            </div>
            <a href="/modules" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-1">
                <span>View Full Modules Directory</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(array_slice($modules, 0, 9) as $mod)
                <div class="bg-slate-900/70 border border-slate-800 hover:border-slate-700 rounded-3xl p-6 transition-all space-y-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-mono text-slate-500 uppercase">{{ $mod['category'] }}</span>
                            @if($mod['is_core'])
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-950 text-indigo-400 border border-indigo-800">CORE</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-950 text-emerald-400 border border-emerald-800">MODULAR</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-white">{{ $mod['name'] }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed mt-1">{{ $mod['description'] }}</p>
                    </div>
                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500">v{{ $mod['version'] }}</span>
                        <a href="/modules/{{ $mod['key'] }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">
                            Explore Module &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="/modules" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 border border-slate-700 hover:border-emerald-500 rounded-xl text-xs font-bold text-white transition-all">
                <span>Browse All 30 Modules & Dependencies</span>
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-20 bg-gradient-to-r from-emerald-900/60 via-slate-900 to-indigo-950/60 border-t border-slate-800">
    <div class="max-w-5xl mx-auto px-4 text-center space-y-6">
        <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight">Ready to Modernize Your Tailoring Atelier?</h2>
        <p class="text-slate-400 text-sm max-w-xl mx-auto">Join world-class boutiques, master tailors, and luxury fashion design studios operating on our SaaS platform.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            <a href="/onboarding" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-2xl shadow-xl shadow-emerald-500/20 text-sm">
                Get Started in 3 Minutes
            </a>
            <a href="/demo" class="px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-2xl text-sm border border-slate-700">
                Test with 8 Pre-Loaded Demo Roles
            </a>
        </div>
    </div>
</section>
@endsection

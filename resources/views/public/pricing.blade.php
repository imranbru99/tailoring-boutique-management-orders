@extends('public.layout')

@section('title', 'Pricing & Subscription Plans | Tailoring OS')

@section('content')
<div class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="text-xs font-mono text-emerald-400 font-bold uppercase tracking-wider block mb-2">Transparent Commercial SaaS Pricing</span>
        <h1 class="text-3xl sm:text-5xl font-black text-white">Simple Plans for Ateliers of Every Scale</h1>
        <p class="text-slate-400 text-sm mt-3">From bespoke corner shops to multi-branch fashion studios. Cancel or upgrade anytime.</p>
    </div>

    <!-- Pricing Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <!-- Starter Plan -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 flex flex-col justify-between space-y-6">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-mono text-slate-400 uppercase">Single Atelier</span>
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-slate-800 text-slate-300">STARTER</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-black text-white">$29</span>
                    <span class="text-slate-400 text-xs">/month</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Essential operating system for single tailoring shops and alteration studios.</p>

                <ul class="mt-6 space-y-3 text-xs text-slate-300">
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> 1 Branch Location</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Up to 5 Tailors & Staff</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Unlimited Customer & Measurement Cards</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> 150 Orders per Month</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Workshop Job Cards & PDF Printing</li>
                </ul>
            </div>

            <a href="/onboarding" class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs text-center transition-all">
                Choose Starter Plan
            </a>
        </div>

        <!-- Pro Plan (Featured) -->
        <div class="bg-gradient-to-b from-slate-900 via-slate-900 to-emerald-950/40 border-2 border-emerald-500 rounded-3xl p-8 flex flex-col justify-between space-y-6 relative shadow-2xl shadow-emerald-500/10">
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-wider">
                Most Popular for Studios
            </div>
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-mono text-emerald-400 uppercase">Fashion Studio</span>
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-emerald-950 text-emerald-400 border border-emerald-800">PRO ATELIER</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-black text-white">$69</span>
                    <span class="text-slate-400 text-xs">/month</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Complete boutique management with POS, fabric roll batches, and customer portal.</p>

                <ul class="mt-6 space-y-3 text-xs text-slate-300">
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Up to 3 Atelier Branches</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Up to 15 Artisans & Tailors</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> 600 Orders per Month</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Point of Sale (POS) & Barcode Support</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Customer Self-Service Portal Access</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> AI Catalog & Description Assistant</li>
                </ul>
            </div>

            <a href="/onboarding" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs text-center shadow-lg shadow-emerald-500/20 transition-all">
                Launch Pro Studio
            </a>
        </div>

        <!-- Enterprise Plan -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 flex flex-col justify-between space-y-6">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-mono text-indigo-400 uppercase">Enterprise</span>
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-indigo-950 text-indigo-400 border border-indigo-800">COUTURE HOUSE</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-black text-white">$149</span>
                    <span class="text-slate-400 text-xs">/month</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Unlimited power for multi-location luxury houses and garment manufacturers.</p>

                <ul class="mt-6 space-y-3 text-xs text-slate-300">
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Unlimited Branches</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Unlimited Staff & Piece-Rate Tailors</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Unlimited Orders & Invoices</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Biometric Attendance Device Sync</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> REST API & Webhooks Access</li>
                    <li class="flex items-center gap-2"><span class="text-emerald-400 font-bold">✓</span> Dedicated Database & SLA Support</li>
                </ul>
            </div>

            <a href="/onboarding" class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs text-center transition-all">
                Contact Enterprise
            </a>
        </div>
    </div>
</div>
@endsection

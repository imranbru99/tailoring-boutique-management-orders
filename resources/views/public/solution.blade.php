@extends('public.layout')

@php
    $titles = [
        'tailoring' => 'Bespoke Tailoring Shops & Master Cutters',
        'boutique' => 'Luxury Boutiques & Ready-to-Wear Retailers',
        'studio' => 'Haute Couture Studios & Fashion Designers',
        'alteration' => 'Garment Alteration, Repair & Fitting Shops',
    ];

    $subtitles = [
        'tailoring' => 'Engineered for precision anatomical measuring, cloth allowances, bench job cards, and immutable order history.',
        'boutique' => 'Built for barcode POS checkout, seasonal collections, fabric roll lot tracking, and client reward points.',
        'studio' => 'Designed for styling appointments, bespoke design portfolios, fitting trial cycles, and VIP client cards.',
        'alteration' => 'Optimized for walk-in repair ticketing, pin marks, tailor seam allowances, and rapid turnaround receipts.',
    ];
@endphp

@section('title', ($titles[$type] ?? 'Industry Solutions') . ' | Tailoring OS')

@section('content')
<div class="py-16 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="text-xs font-mono text-emerald-400 font-bold uppercase tracking-wider block mb-2">Tailored Business Solution</span>
        <h1 class="text-3xl sm:text-5xl font-black text-white">{{ $titles[$type] ?? 'Industry Solution' }}</h1>
        <p class="text-slate-400 text-base mt-4">{{ $subtitles[$type] ?? '' }}</p>
    </div>

    <!-- 3 Pillars Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-3xl space-y-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold">✓</div>
            <h3 class="font-bold text-white text-base">Zero Measurement Chaos</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                Standardize measurements across staff. Store unlimited body parameters with inch/cm conversion and automatic posture adjustment notes.
            </p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-3xl space-y-3">
            <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center font-bold">✓</div>
            <h3 class="font-bold text-white text-base">Bench Accountability</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                Know exactly which artisan is cutting, stitching, or pressing every garment. Track piece-rate commissions and labor hours seamlessly.
            </p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-3xl space-y-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">✓</div>
            <h3 class="font-bold text-white text-base">Client Trust & Invoicing</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                Automated advance payment receipts, SMS order milestone triggers, and multilingual PDF invoices in English, Bengali, Arabic, Hindi, or Urdu.
            </p>
        </div>
    </div>

    <!-- CTA Box -->
    <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 border border-slate-800 rounded-3xl p-8 sm:p-12 text-center space-y-6">
        <h2 class="text-2xl sm:text-4xl font-black text-white">Experience Tailoring OS for Your Atelier</h2>
        <p class="text-slate-400 text-xs sm:text-sm max-w-xl mx-auto">Launch your business in 3 minutes or test our live demo environment pre-loaded with sample garments and orders.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/onboarding" class="px-8 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs shadow-lg shadow-emerald-500/20">
                Setup {{ ucfirst($type) }} System Now
            </a>
            <a href="/demo" class="px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs border border-slate-700">
                Open Role Demo
            </a>
        </div>
    </div>
</div>
@endsection

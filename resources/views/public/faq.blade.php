@extends('public.layout')

@section('title', 'Frequently Asked Questions (FAQ)')

@section('content')
<div class="py-16 bg-slate-900 border-b border-slate-800 text-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
            Got Questions?
        </span>
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight font-serif">
            Frequently Asked Questions
        </h1>
        <p class="mt-4 text-xl text-slate-300 max-w-2xl mx-auto">
            Everything you need to know about our tailoring operating system, multi-tenancy, measurement security, and module management.
        </p>
    </div>
</div>

<div class="py-16 bg-slate-950 text-slate-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="text-lg font-bold text-white mb-2">How does measurement snapshot immortality work?</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
                When an order item is confirmed, all active measurement parameters (such as collar, chest, waist, sleeve, inseam, crotch depth) are captured and saved as an immutable JSON snapshot inside the order item itself. If a client gains or loses weight next year and updates their customer profile, your past order specifications and job cards will never change.
            </p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="text-lg font-bold text-white mb-2">Can I turn off modules I don't use?</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
                Yes! Our dynamic Module Management Engine lets boutique owners disable features like Alterations, Biometric Attendance, Marketing Campaigns, or AI Catalog with a single click. When disabled, the navigation items, route permissions, and widgets disappear completely. Furthermore, your historical data is never deleted.
            </p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="text-lg font-bold text-white mb-2">Can we support multiple boutique branches?</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
                Absolutely. Each tenant can register unlimited branches. You can restrict staff users and inventory rolls to a specific branch, or view unified business analytics and financial cashflows across the whole enterprise.
            </p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="text-lg font-bold text-white mb-2">Does the system support Arabic, Bengali, or other non-Latin scripts?</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
                Yes. Through our integrated <code class="text-emerald-400">laravel-unicode-pdf</code> and <code class="text-emerald-400">laravel-universal-slug</code> engines, all generated job cards, customer invoices, and measurement slips properly render complex scripts and right-to-left (RTL) formatting (including Bengali, Arabic, Urdu, and Hindi).
            </p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="text-lg font-bold text-white mb-2">How does the 18-stage production workflow benefit our workshop?</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
                The 18 discrete stages (from draft and fabric sourcing to cutting, stitching, embroidery, quality control, fitting, and delivery) eliminate the chaos of "where is this suit right now?". Master tailors can only claim cutting or stitching once the job card is officially assigned, and delivery slips cannot be dispatched until quality inspections pass.
            </p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="text-lg font-bold text-white mb-2">Is there an API for external eCommerce or mobile apps?</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
                Yes. A full REST API secured by Laravel Sanctum bearer tokens is available at <code class="text-emerald-400">/api/v1/</code> for customer lookup, measurement sync, order status tracking, and biometric attendance clocks.
            </p>
        </div>

        <div class="mt-12 text-center">
            <p class="text-slate-400">Still have questions?</p>
            <a href="{{ route('contact') }}" class="mt-2 inline-block text-emerald-400 font-bold hover:underline">
                Contact our solutions advisory team →
            </a>
        </div>
    </div>
</div>
@endsection

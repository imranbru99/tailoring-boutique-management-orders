@extends('public.layout')

@section('title', 'Terms of Service — Legal & Usage Agreement')

@section('content')
<div class="py-16 bg-slate-900 border-b border-slate-800 text-white relative">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
            Legal Terms
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight font-serif">Terms of Service</h1>
        <p class="mt-2 text-slate-400 text-sm">Last updated: November 2026</p>
    </div>
</div>

<div class="py-16 bg-slate-950 text-slate-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 text-sm leading-relaxed text-slate-400">
        <section>
            <h2 class="text-xl font-bold text-white font-serif mb-3">1. Agreement to Terms</h2>
            <p>
                By accessing or using Tailoring & Boutique Management SaaS ("the Service"), operated by ImranDev Solutions Ltd. (developer website: <a href="https://imrandev.bd" class="text-emerald-400 hover:underline">https://imrandev.bd</a>), you agree to be bound by these Terms of Service.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-white font-serif mb-3">2. Multi-Tenant Data Sovereignty & Customer Privacy</h2>
            <p>
                Each business tenant operates in a logically isolated environment. Your customer measurement data, order specifications, financial records, and employee attendance data remain strictly confidential to your tenant organization. We do not sell, license, or expose client bodily measurements or proprietary garment patterns.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-white font-serif mb-3">3. Service Levels & Availability</h2>
            <p>
                We strive for 99.9% uptime across our cloud infrastructure. Scheduled maintenance windows will be communicated at least 48 hours in advance through the tenant administrator noticeboard.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-white font-serif mb-3">4. Billing & Subscriptions</h2>
            <p>
                Subscription fees for the SaaS platform are billed in advance on a monthly or annual basis. Active module licenses and branch limits correspond to your chosen tier.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-white font-serif mb-3">5. Developer & Author Contact</h2>
            <p>
                For legal inquiries, dispute resolution, or enterprise on-premise licensing, please contact:
                <br><strong class="text-white">Lead Architect:</strong> Imran Ahmed
                <br><strong class="text-white">Email:</strong> me@imrandev.bd
                <br><strong class="text-white">Website:</strong> <a href="https://imrandev.bd" class="text-emerald-400 hover:underline">https://imrandev.bd</a>
            </p>
        </section>
    </div>
</div>
@endsection

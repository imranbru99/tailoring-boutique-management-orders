@extends('public.layout')

@section('title', 'Privacy Policy — Data Protection Standards')

@section('content')
<div class="py-16 bg-slate-900 border-b border-slate-800 text-white relative">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
            Data Governance
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight font-serif">Privacy Policy</h1>
        <p class="mt-2 text-slate-400 text-sm">Last updated: November 2026</p>
    </div>
</div>

<div class="py-16 bg-slate-950 text-slate-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 text-sm leading-relaxed text-slate-400">
        <section>
            <h2 class="text-xl font-bold text-white font-serif mb-3">1. Information We Collect</h2>
            <p>
                To provide boutique operations, the platform processes:
            </p>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li><strong class="text-white">Customer Data:</strong> Name, phone number, physical address, and communication preferences.</li>
                <li><strong class="text-white">Body Dimensions:</strong> Anatomical measurements and posture notes recorded for garment cutting.</li>
                <li><strong class="text-white">Transactional Data:</strong> Order line items, fabric references, invoice amounts, and payment tokens.</li>
                <li><strong class="text-white">Staff Attendance:</strong> Clock-in/out timestamps and hardware biometric device sync IDs.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-xl font-bold text-white font-serif mb-3">2. How Data is Isolated</h2>
            <p>
                Each tenant's data is guarded by multi-tenant database scoping (<code class="text-emerald-400">TenantScope</code>). Under no circumstance can staff or managers from one boutique access records from another boutique.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-white font-serif mb-3">3. AI Usage & Confidentiality</h2>
            <p>
                When utilizing AI catalog or description generation (via <code class="text-emerald-400">laravel-ai-hub</code>), only product catalog prompts and fabric composition text are dispatched to the selected AI provider. Customer names, personal physical dimensions, and payment card numbers are never sent to external AI models.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-white font-serif mb-3">4. Data Controller & Inquiries</h2>
            <p>
                For data export, GDPR deletion requests, or compliance auditing:
                <br><strong class="text-white">Developer & Data Protection Officer:</strong> Imran Ahmed
                <br><strong class="text-white">Email:</strong> me@imrandev.bd
                <br><strong class="text-white">Website:</strong> <a href="https://imrandev.bd" class="text-emerald-400 hover:underline">https://imrandev.bd</a>
            </p>
        </section>
    </div>
</div>
@endsection

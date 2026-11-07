@extends('public.layout')

@section('title', 'Contact & Advisory — Concierge Setup')

@section('content')
<div class="py-16 bg-slate-900 border-b border-slate-800 text-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
            Atelier Support & Enterprise Inquiries
        </span>
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight font-serif">
            Let’s Discuss Your Atelier
        </h1>
        <p class="mt-4 text-xl text-slate-300 max-w-2xl mx-auto">
            Whether you operate a boutique studio in London, a bridal atelier in Dubai, or a multi-branch bespoke chain in Dhaka, our team is here to assist.
        </p>
    </div>
</div>

<div class="py-16 bg-slate-950 text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Form -->
            <div class="p-8 rounded-2xl bg-slate-900 border border-slate-800">
                <h3 class="text-2xl font-bold text-white font-serif mb-6">Send an Inquiry</h3>
                <form class="space-y-6" onsubmit="event.preventDefault(); alert('Thank you for contacting us! An atelier consultant will reach out within 2 hours.');">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">First Name</label>
                            <input type="text" required placeholder="Alexander" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Last Name</label>
                            <input type="text" required placeholder="Wright" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-emerald-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Business Email</label>
                        <input type="email" required placeholder="alexander@savilerow.test" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-emerald-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Business Model</label>
                        <select class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-emerald-500 text-sm">
                            <option>Bespoke Tailoring Shop</option>
                            <option>High Fashion & Bridal Boutique</option>
                            <option>Alteration & Garment Repair Service</option>
                            <option>Multi-Branch Luxury Chain</option>
                            <option>Custom Shirtmaker / Suit Specialist</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Message or Operational Requirement</label>
                        <textarea rows="4" required placeholder="Describe your current production volume, branch count, or software migration requirements..." class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-emerald-500 text-sm"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold transition">
                        Submit Inquiry
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="space-y-8 flex flex-col justify-between">
                <div class="space-y-6">
                    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <div class="text-emerald-400 text-xl mb-2">📍 Worldwide Headquarters</div>
                        <p class="text-white font-medium">ImranDev Bespoke Solutions Ltd.</p>
                        <p class="text-sm text-slate-400 mt-1">Gulshan-2 Commercial Area, Dhaka, Bangladesh</p>
                        <a href="https://imrandev.bd" target="_blank" class="inline-block mt-3 text-xs text-emerald-400 hover:underline">
                            https://imrandev.bd
                        </a>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <div class="text-emerald-400 text-xl mb-2">✉️ Direct Engineering Inquiries</div>
                        <p class="text-white font-medium">Lead Architect: Imran Ahmed</p>
                        <p class="text-sm text-slate-400 mt-1">Email: me@imrandev.bd</p>
                        <p class="text-xs text-slate-500 mt-2">Guaranteed reply within 24 business hours for enterprise evaluations.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <div class="text-emerald-400 text-xl mb-2">⚡ Live Interactive Testing</div>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            No waiting needed. Test our fully provisioned 8-role sandbox immediately with pre-loaded tailoring orders, measurement snapshots, and live PDF rendering.
                        </p>
                        <a href="{{ route('demo') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold hover:bg-emerald-500 hover:text-slate-950 transition">
                            Launch Sandbox Now →
                        </a>
                    </div>
                </div>

                <div class="text-xs text-slate-500 border-t border-slate-800 pt-6">
                    Protected by enterprise SLA, SOC-2 readiness standards, and isolated tenant databases.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

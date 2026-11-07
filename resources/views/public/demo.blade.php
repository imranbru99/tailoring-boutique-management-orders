@extends('public.layout')

@section('title', 'Interactive Live Demo — 8 Role Personas')

@section('content')
<div class="py-16 bg-slate-900 border-b border-slate-800 text-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-4">
            Hands-on Evaluation
        </span>
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight font-serif">
            Interactive Role-Based Live Demo
        </h1>
        <p class="mt-4 text-xl text-slate-300 max-w-3xl mx-auto">
            Experience the system through 8 distinct viewpoints: from platform super admin and boutique owner down to master tailor, receptionist, and customer.
        </p>

        <div class="mt-6 inline-flex items-center gap-3 px-5 py-2.5 rounded-xl bg-slate-800/90 border border-slate-700 text-slate-300 text-sm">
            <span class="font-semibold text-white">Default Demo Password for all personas:</span>
            <code class="px-2 py-0.5 rounded bg-slate-950 font-mono text-emerald-400 font-bold">Demo@12345!</code>
        </div>
    </div>
</div>

<div class="py-16 bg-slate-950 text-slate-300 min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('error'))
            <div class="mb-8 p-4 rounded-xl bg-red-900/30 border border-red-800 text-red-200 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($demoUsers as $user)
                <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-col justify-between hover:border-emerald-500/50 transition-all duration-300 shadow-xl group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $user['panel'] }}
                            </span>
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm group-hover:scale-110 transition-transform">
                                @if(str_contains($user['role'], 'Admin')) 👑
                                @elseif(str_contains($user['role'], 'Owner')) 🏛️
                                @elseif(str_contains($user['role'], 'Manager')) 👔
                                @elseif(str_contains($user['role'], 'Tailor')) ✂️
                                @elseif(str_contains($user['role'], 'Receptionist')) 📋
                                @elseif(str_contains($user['role'], 'Inventory')) 📦
                                @elseif(str_contains($user['role'], 'Accountant')) 💰
                                @else 👤
                                @endif
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-white font-serif mb-1 group-hover:text-emerald-400 transition-colors">
                            {{ $user['role'] }}
                        </h3>
                        <p class="text-xs font-mono text-emerald-400/80 mb-4">{{ $user['email'] }}</p>

                        <p class="text-sm text-slate-400 leading-relaxed mb-6">
                            {{ $user['description'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80">
                        <form method="POST" action="{{ route('demo.login') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $user['email'] }}">
                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-emerald-500 text-white hover:text-slate-950 font-bold text-sm tracking-wide transition flex items-center justify-center gap-2 group-hover:bg-emerald-500 group-hover:text-slate-950">
                                <span>1-Click Launch Persona</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16 p-8 rounded-2xl bg-slate-900 border border-slate-800 text-slate-400 text-sm">
            <h4 class="font-bold text-white text-base mb-2">💡 Demo Mode Capabilities & Sandbox Notice</h4>
            <p class="leading-relaxed">
                The demo dataset is seeded with our flagship sample boutique, <strong>"Elegant Stitch Boutique"</strong>, featuring 2 physical branches (Gulshan Main Atelier & Banani Downtown Studio). Feel free to test order workflows, generate multilingual PDF documents (job cards, delivery slips, invoices), submit fittings, adjust inventory rolls, and toggle modules inside the Module Manager.
            </p>
        </div>
    </div>
</div>
@endsection

@extends('public.layout')

@section('title', 'All 30 Business Modules | Tailoring OS Directory')

@section('content')
<div class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="text-xs font-mono text-emerald-400 font-bold uppercase tracking-wider block mb-2">Centralized Architecture Directory</span>
        <h1 class="text-3xl sm:text-5xl font-black text-white">Complete 30 Business Modules</h1>
        <p class="text-slate-400 text-sm mt-3">
            Every operational feature in Tailoring OS operates as an independent module. Explore individual capabilities, dependency constraints, and business use cases.
        </p>
    </div>

    <!-- Grouped Modules by Category -->
    @php
        $categories = [
            'core' => 'Core Business Foundation',
            'operations' => 'Workshop Production & Atelier Operations',
            'sales' => 'Point of Sale (POS) & Counter Sales',
            'finance' => 'Billing, Invoices & Financial Cashbook',
            'hr' => 'Artisans & Human Resources',
            'marketing' => 'CRM Automation & Customer Retention',
            'ai' => 'AI Intelligence & Design Studio',
            'system' => 'System, Platform & Integrations',
        ];
    @endphp

    <div class="space-y-16">
        @foreach($categories as $catKey => $catTitle)
            @php
                $catModules = array_filter($modules, fn($m) => $m['category'] === $catKey);
            @endphp
            @if(!empty($catModules))
                <div>
                    <h2 class="text-xl font-bold text-white mb-6 pb-2 border-b border-slate-800 flex items-center justify-between">
                        <span>{{ $catTitle }}</span>
                        <span class="text-xs font-mono text-slate-500 font-normal">{{ count($catModules) }} Modules</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($catModules as $mod)
                            <div class="bg-slate-900/70 border border-slate-800 hover:border-slate-700 rounded-3xl p-6 flex flex-col justify-between space-y-4 shadow-sm hover:shadow-md transition-all">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <code class="text-xs font-mono text-emerald-400 font-bold">{{ $mod['key'] }}</code>
                                        @if($mod['is_core'])
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-950 text-indigo-400 border border-indigo-800">CORE ESSENTIAL</span>
                                        @else
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-400">MODULAR</span>
                                        @endif
                                    </div>
                                    <h3 class="text-lg font-bold text-white">{{ $mod['name'] }}</h3>
                                    <p class="text-xs text-slate-400 leading-relaxed mt-2">{{ $mod['description'] }}</p>

                                    @if(!empty($mod['dependencies']))
                                        <div class="mt-4 pt-3 border-t border-slate-800 text-[11px] text-slate-500">
                                            <span class="text-slate-400 font-semibold">Requires:</span>
                                            <span>{{ implode(', ', $mod['dependencies']) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                                    <span class="text-[11px] text-slate-500 font-mono">v{{ $mod['version'] }}</span>
                                    <a href="/modules/{{ $mod['key'] }}" class="text-xs font-bold text-emerald-400 hover:text-emerald-300">
                                        Specifications &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection

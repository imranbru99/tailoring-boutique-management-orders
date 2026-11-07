@extends('public.layout')

@section('title', $module['name'] . ' | Tailoring OS Module Specs')

@section('content')
<div class="py-16 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="/modules" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-4">
            &larr; Return to Modules Catalog
        </a>
        <div class="flex flex-wrap items-center gap-3 mb-3">
            <span class="text-xs font-mono uppercase px-2.5 py-1 rounded-lg bg-emerald-950 text-emerald-400 border border-emerald-800">
                {{ $module['category'] }}
            </span>
            @if($module['is_core'])
                <span class="text-xs font-mono uppercase px-2.5 py-1 rounded-lg bg-indigo-950 text-indigo-400 border border-indigo-800">
                    Core Locked
                </span>
            @endif
            <span class="text-xs font-mono text-slate-500">v{{ $module['version'] }}</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-white">{{ $module['name'] }}</h1>
        <p class="text-slate-400 text-base mt-3 max-w-3xl leading-relaxed">{{ $module['description'] }}</p>
    </div>

    <!-- Main Specs Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 space-y-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 border-b border-slate-800 pb-8 text-xs">
            <div>
                <span class="text-slate-500 block">Module Identifier:</span>
                <code class="text-emerald-400 font-mono text-sm block mt-1 font-bold">{{ $module['key'] }}</code>
            </div>
            <div>
                <span class="text-slate-500 block">Tier Minimum:</span>
                <span class="text-white font-mono text-sm block mt-1 uppercase font-bold">{{ $module['min_plan'] }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Deactivation Policy:</span>
                <span class="text-slate-300 text-sm block mt-1">Zero Data Loss Guaranteed</span>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-bold text-white mb-3">Architectural Capabilities</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-300">
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                    <strong class="text-emerald-400 block font-bold">Tenant-Scoped Isolation</strong>
                    <p class="text-slate-400 leading-relaxed">Operates strictly with Eloquent Global Tenancy Scopes to guarantee cross-tenant data privacy.</p>
                </div>
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                    <strong class="text-emerald-400 block font-bold">State Machine Validation</strong>
                    <p class="text-slate-400 leading-relaxed">Protected by backend business policies, transition validation, and audit tracking.</p>
                </div>
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                    <strong class="text-emerald-400 block font-bold">Dynamic Navigation Binding</strong>
                    <p class="text-slate-400 leading-relaxed">Hides or registers Filament resources and dashboard widgets dynamically per tenant subscription.</p>
                </div>
                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                    <strong class="text-emerald-400 block font-bold">REST API Endpoint Sync</strong>
                    <p class="text-slate-400 leading-relaxed">Protected under `/api/v1/{{ $module['route_prefix'] ?? $module['key'] }}` with Sanctum rate limits.</p>
                </div>
            </div>
        </div>

        @if(!empty($module['dependencies']))
            <div>
                <h3 class="text-lg font-bold text-white mb-3">Module Dependencies</h3>
                <div class="p-4 bg-amber-950/20 border border-amber-900/40 rounded-2xl text-xs text-amber-300 space-y-2">
                    <p>To activate <strong>{{ $module['name'] }}</strong>, the following prerequisite modules must be enabled in your atelier settings:</p>
                    <ul class="list-disc list-inside font-mono">
                        @foreach($module['dependencies'] as $depKey)
                            <li>{{ $depKey }} ({{ $allModules[$depKey]['name'] ?? $depKey }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-slate-500">Ready to test this module in action?</span>
            <div class="flex gap-3">
                <a href="/demo" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs">
                    Try in Demo Sandbox
                </a>
                <a href="/onboarding" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs">
                    Activate for My Business
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

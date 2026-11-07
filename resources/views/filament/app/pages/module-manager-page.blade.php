<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Top Description Banner -->
        <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Dynamic Business Operating System Engine</h2>
                    <p class="text-emerald-100 text-sm mt-1 max-w-2xl">
                        Customize your atelier workflow by toggling independent modules. Core modules remain permanently locked for business integrity. When a module is deactivated, its navigation and widgets are safely hidden, while all underlying records are strictly preserved.
                    </p>
                </div>
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20">
                    <x-heroicon-o-shield-check class="w-6 h-6 text-emerald-300" />
                    <div class="text-xs">
                        <span class="block font-bold">Zero Data Loss Policy</span>
                        <span class="text-white/80">Safe Deactivation</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Filter Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-800 pb-3">
            @foreach($this->getCategories() as $catKey => $catLabel)
                <button
                    wire:click="$set('selectedCategory', '{{ $catKey }}')"
                    type="button"
                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-all {{ $selectedCategory === $catKey ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                >
                    {{ $catLabel }}
                </button>
            @endforeach
        </div>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->modules as $mod)
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <!-- Header: Icon, Name, Badges -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $mod['is_enabled'] ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }}">
                                    @svg($mod['icon'] ?? 'heroicon-o-cube', 'w-6 h-6')
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-base leading-snug">
                                        {{ $mod['name'] }}
                                    </h3>
                                    <span class="text-[11px] font-mono text-gray-400">v{{ $mod['version'] }} &bull; {{ ucfirst($mod['category']) }}</span>
                                </div>
                            </div>
                            <div>
                                @if($mod['is_core'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                        CORE
                                    </span>
                                @elseif($mod['is_enabled'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                        ACTIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        DISABLED
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                            {{ $mod['description'] }}
                        </p>

                        <!-- Stats & Metadata -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 mb-4 space-y-1.5 text-xs text-gray-600 dark:text-gray-300">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Live Business Records:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($mod['record_count']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Tier Entitlement:</span>
                                <span class="font-mono text-indigo-600 dark:text-indigo-400 uppercase text-[10px]">{{ $mod['min_plan'] }}</span>
                            </div>
                            @if(!empty($mod['dependencies']))
                                <div class="pt-1 border-t border-gray-200 dark:border-gray-700 text-[11px]">
                                    <span class="text-gray-500">Requires:</span>
                                    <span class="font-medium text-amber-700 dark:text-amber-400">{{ implode(', ', $mod['dependencies']) }}</span>
                                </div>
                            @endif
                            @if(!empty($mod['active_dependents']))
                                <div class="text-[11px] text-blue-600 dark:text-blue-400">
                                    <span>Used by active: {{ implode(', ', $mod['active_dependents']) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <span class="text-[11px] text-gray-400">
                            Key: <code class="text-gray-500 font-mono">{{ $mod['key'] }}</code>
                        </span>

                        @if($mod['is_core'])
                            <button disabled type="button" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed">
                                Essential Locked
                            </button>
                        @elseif($mod['is_enabled'])
                            <button
                                wire:click="toggleModule('{{ $mod['key'] }}')"
                                type="button"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-400 dark:hover:bg-rose-900/60 transition-colors"
                            >
                                Deactivate
                            </button>
                        @else
                            <button
                                wire:click="toggleModule('{{ $mod['key'] }}')"
                                type="button"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm transition-colors"
                            >
                                Activate Module
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>

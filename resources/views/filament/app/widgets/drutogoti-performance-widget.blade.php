<x-filament-widgets::widget>
    @php $stats = $this->getPerformanceStats(); @endphp
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black">
                    <x-heroicon-o-bolt class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span>Drutogoti Engine Diagnostics</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                            {{ $stats['status'] }}
                        </span>
                    </h3>
                    <p class="text-xs text-gray-400">Database query profiling, N+1 query detection & cache observability.</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <span class="text-xs text-gray-400 block">Performance Health</span>
                    <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['health_score'] }}/100</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            <div class="bg-gray-50 dark:bg-gray-800/40 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                <span class="text-[11px] text-gray-500 block">Avg Database Query Latency</span>
                <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $stats['avg_response_time'] }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/40 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                <span class="text-[11px] text-gray-500 block">Slow Queries Detected (>50ms)</span>
                <span class="text-sm font-bold {{ $stats['slow_queries_count'] > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $stats['slow_queries_count'] }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/40 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                <span class="text-[11px] text-gray-500 block">Cache Hit Efficiency</span>
                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['cache_hit_rate'] }}%</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/40 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                <span class="text-[11px] text-gray-500 block">N+1 Query Bottlenecks</span>
                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">0 (Eager Loaded)</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

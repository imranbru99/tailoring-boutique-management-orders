<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Onboarding Wizard | Tailoring & Boutique Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen py-12 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto">
        <!-- Logo / Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold mb-3">
                <span>Tailoring Operating System 2.0</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white">Setup Your Atelier in 3 Minutes</h1>
            <p class="text-slate-400 text-sm mt-2 max-w-lg mx-auto">Configure your business archetype, flagship branch, measurement standards, and modular feature entitlements.</p>
        </div>

        <form action="{{ route('onboarding.process') }}" method="POST" class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-3xl p-6 sm:p-10 shadow-2xl space-y-8">
            @csrf

            <!-- Section 1: Business Identity -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-500 text-slate-900 flex items-center justify-center text-xs font-bold">1</span>
                    Business Information & Archetype
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Atelier / Boutique Name *</label>
                        <input type="text" name="business_name" required value="Sartoria Royale Atelier" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Business Operating Model *</label>
                        <select name="business_type" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500">
                            <option value="boutique">Boutique & Ready-To-Wear Studio</option>
                            <option value="tailoring_shop">Custom Tailoring Shop</option>
                            <option value="fashion_studio">Haute Couture Fashion Studio</option>
                            <option value="bridal_studio">Bridal Studio</option>
                            <option value="alteration_shop">Alteration & Fitting Specialist</option>
                            <option value="uniform_manufacturer">Uniform Manufacturer</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Flagship Branch & Standards -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-500 text-slate-900 flex items-center justify-center text-xs font-bold">2</span>
                    Branch & Localization Standards
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Primary Branch Name *</label>
                        <input type="text" name="branch_name" required value="Downtown Flagship Atelier" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Currency Code & Symbol *</label>
                        <div class="flex gap-2">
                            <input type="text" name="currency" required value="USD" class="w-20 bg-slate-900/80 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-center">
                            <input type="text" name="currency_symbol" required value="$" class="w-16 bg-slate-900/80 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-center">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Measurement Unit *</label>
                        <select name="measurement_unit" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm">
                            <option value="inch">Inches (in)</option>
                            <option value="cm">Centimeters (cm)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <input type="hidden" name="timezone" value="UTC">
                </div>
            </div>

            <!-- Section 3: Owner Credentials -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-500 text-slate-900 flex items-center justify-center text-xs font-bold">3</span>
                    Master Account (Business Owner)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name *</label>
                        <input type="text" name="owner_name" required value="Master Tailor Imran" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Login Email *</label>
                        <input type="email" name="owner_email" required value="owner@tailor.test" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Password *</label>
                        <input type="password" name="owner_password" required value="Demo@12345!" class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Section 4: Module Selection -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-700 pb-2">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-500 text-slate-900 flex items-center justify-center text-xs font-bold">4</span>
                        Initial Module Activation
                    </h3>
                    <span class="text-xs text-slate-400">Can be toggled anytime from Settings</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-2">
                    @foreach($modules as $mKey => $mod)
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-slate-900/60 border border-slate-700/80 cursor-pointer hover:border-emerald-500/60 transition-all">
                            <input
                                type="checkbox"
                                name="selected_modules[]"
                                value="{{ $mKey }}"
                                {{ $mod['is_core'] || $mod['default_enabled'] ? 'checked' : '' }}
                                {{ $mod['is_core'] ? 'disabled' : '' }}
                                class="mt-0.5 rounded border-slate-700 text-emerald-500 focus:ring-emerald-500 bg-slate-800"
                            >
                            <div>
                                <span class="font-bold text-xs text-slate-200 block">
                                    {{ $mod['name'] }}
                                    @if($mod['is_core'])
                                        <span class="text-[10px] text-emerald-400 font-mono">(Core Locked)</span>
                                    @endif
                                </span>
                                <span class="text-[11px] text-slate-400 line-clamp-1">{{ $mod['description'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="/" class="text-xs text-slate-400 hover:text-white transition-colors">&larr; Back to Public Portal</a>
                <button
                    type="submit"
                    class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-900 font-bold rounded-2xl shadow-lg shadow-emerald-500/25 transition-all text-sm"
                >
                    Complete Setup & Launch Atelier &rarr;
                </button>
            </div>
        </form>
    </div>
</body>
</html>

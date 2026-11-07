<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tailoring and Boutique Management System | Enterprise SaaS OS')</title>
    <meta name="description" content="Enterprise-grade, modular, AI-powered multi-tenant business management software for tailoring shops, luxury boutiques, fashion design studios, and garment manufacturers.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased selection:bg-emerald-500 selection:text-slate-950 min-h-screen flex flex-col">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 text-white text-xs font-semibold py-2 px-4 text-center">
        <span>✨ Version 2.0 Live &bull; Powered by Laravel 12, Filament 3, Multi-Tenant Architecture & AI Studio</span>
        <a href="/demo" class="underline ml-2 font-bold hover:text-emerald-100">Explore Interactive Demo &rarr;</a>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-slate-950/80 border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-slate-950 font-black shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.242 3 3 0 004.243-4.242zm0-5.758a3 3 0 10-4.243-4.242 3 3 0 004.243 4.242z"/></svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold text-white tracking-tight block">Tailoring OS</span>
                        <span class="text-[10px] text-emerald-400 font-mono tracking-wider block uppercase">Enterprise Fashion SaaS</span>
                    </div>
                </a>

                <nav class="hidden lg:flex items-center gap-6 text-sm font-semibold text-slate-300">
                    <a href="/modules" class="hover:text-emerald-400 transition-colors">All 30 Modules</a>
                    <div class="relative group">
                        <button class="flex items-center gap-1 hover:text-emerald-400 transition-colors py-2">
                            <span>Solutions</span>
                            <svg class="w-4 h-4 text-slate-500 group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute top-full left-0 hidden group-hover:block w-64 bg-slate-900 border border-slate-800 rounded-2xl p-2 shadow-2xl space-y-1">
                            <a href="/solutions/tailoring" class="block p-2.5 rounded-xl hover:bg-slate-800 text-xs text-slate-200">
                                <span class="font-bold block text-white">Bespoke Tailoring</span>
                                <span class="text-slate-400 text-[11px]">Measurement cards & job cards</span>
                            </a>
                            <a href="/solutions/boutique" class="block p-2.5 rounded-xl hover:bg-slate-800 text-xs text-slate-200">
                                <span class="font-bold block text-white">Fashion Boutique</span>
                                <span class="text-slate-400 text-[11px]">Ready-to-wear POS & fabric rolls</span>
                            </a>
                            <a href="/solutions/studio" class="block p-2.5 rounded-xl hover:bg-slate-800 text-xs text-slate-200">
                                <span class="font-bold block text-white">Haute Couture Studio</span>
                                <span class="text-slate-400 text-[11px]">Appointments & design portfolio</span>
                            </a>
                            <a href="/solutions/alteration" class="block p-2.5 rounded-xl hover:bg-slate-800 text-xs text-slate-200">
                                <span class="font-bold block text-white">Alteration Specialist</span>
                                <span class="text-slate-400 text-[11px]">Fitting trials & pin rework</span>
                            </a>
                        </div>
                    </div>
                    <a href="/pricing" class="hover:text-emerald-400 transition-colors">Pricing & Plans</a>
                    <a href="/demo" class="hover:text-emerald-400 transition-colors flex items-center gap-1 text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Demo
                    </a>
                    <a href="/docs" class="hover:text-emerald-400 transition-colors">Architecture</a>
                    <a href="/about" class="hover:text-emerald-400 transition-colors">About</a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <a href="/app" class="text-xs font-bold text-slate-300 hover:text-white px-3 py-2 rounded-xl transition-colors">
                    Login
                </a>
                <a href="/onboarding" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                    Start Free Trial
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Global Premium Footer -->
    <footer class="bg-slate-950 border-t border-slate-900 pt-16 pb-12 text-slate-400 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 mb-12">
                <div class="col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-slate-950 font-black">T</div>
                        <span class="text-base font-bold text-white">Tailoring & Boutique Management</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed max-w-sm mb-4">
                        Enterprise multi-tenant fashion software designed for precision bespoke tailors, luxury boutiques, couture ateliers, and apparel manufacturers worldwide.
                    </p>
                    <div class="text-[11px] text-slate-500 space-y-1">
                        <div>Developed by: <strong>Imran Ahmed</strong> (<a href="https://imrandev.bd" target="_blank" class="text-emerald-400 hover:underline">imrandev.bd</a>)</div>
                        <div>Git Author: Imran Ahmed &bull; me@imrandev.bd</div>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-white text-sm mb-3">Modular System</h4>
                    <ul class="space-y-2">
                        <li><a href="/modules" class="hover:text-emerald-400">All 30 Modules</a></li>
                        <li><a href="/modules/measurements" class="hover:text-emerald-400">Measurement Cards</a></li>
                        <li><a href="/modules/orders" class="hover:text-emerald-400">Order Workflow</a></li>
                        <li><a href="/modules/production" class="hover:text-emerald-400">Job Cards & Kanban</a></li>
                        <li><a href="/modules/fabrics" class="hover:text-emerald-400">Fabric Roll Inventory</a></li>
                        <li><a href="/modules/billing" class="hover:text-emerald-400">Multilingual Invoices</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white text-sm mb-3">Industry Solutions</h4>
                    <ul class="space-y-2">
                        <li><a href="/solutions/tailoring" class="hover:text-emerald-400">Bespoke Tailoring</a></li>
                        <li><a href="/solutions/boutique" class="hover:text-emerald-400">Ready-to-Wear Boutiques</a></li>
                        <li><a href="/solutions/studio" class="hover:text-emerald-400">Fashion Design Studios</a></li>
                        <li><a href="/solutions/alteration" class="hover:text-emerald-400">Alteration Shops</a></li>
                        <li><a href="/pricing" class="hover:text-emerald-400">Subscription Plans</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white text-sm mb-3">Resources & Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="/demo" class="hover:text-emerald-400 font-semibold text-emerald-400">Interactive Demo</a></li>
                        <li><a href="/docs" class="hover:text-emerald-400">Developer Docs & API</a></li>
                        <li><a href="/faq" class="hover:text-emerald-400">FAQ & Knowledge Base</a></li>
                        <li><a href="/contact" class="hover:text-emerald-400">Contact Support</a></li>
                        <li><a href="/privacy" class="hover:text-emerald-400">Privacy Policy</a></li>
                        <li><a href="/terms" class="hover:text-emerald-400">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-900 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
                <div>&copy; {{ date('Y') }} Tailoring and Boutique Management System. All rights reserved. Built with Laravel 12 & Filament 3.</div>
                <div class="flex items-center gap-4">
                    <span>English</span>
                    <span>বাংলা (Bengali)</span>
                    <span>العربية (Arabic)</span>
                    <span>हिन्दी (Hindi)</span>
                    <span>اردو (Urdu)</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>

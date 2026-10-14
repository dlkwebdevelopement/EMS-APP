<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DLK EMS - Employee Management System')</title>
    <meta name="description" content="@yield('meta_description', 'DLK Technologies Employee Management System - Comprehensive HR, Attendance, Payroll & Team Operations Portal')">
    <link rel="icon" type="image/png" href="{{ asset('storage/assets/icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols Outlined -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .bg-green-gradient {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }

        .bg-green-soft {
            background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 100%);
        }

        .text-gradient-green {
            background: linear-gradient(135deg, #047857 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(16, 185, 129, 0.12);
        }

        .green-card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .green-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(5, 150, 105, 0.1), 0 8px 10px -6px rgba(5, 150, 105, 0.05);
            border-color: #a7f3d0;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #059669;
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen flex flex-col justify-between selection:bg-brand-100 selection:text-brand-900 bg-white">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 glass-nav transition-all duration-300">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 text-decoration-none group">
                <div class="w-11 h-11 rounded-xl bg-brand-600 flex items-center justify-center shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform overflow-hidden p-1">
                    <img src="{{ asset('storage/assets/icon.png') }}" alt="DLK EMS" class="w-full h-full object-contain filter brightness-0 invert">
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 font-heading block leading-none">
                        DLK <span class="text-brand-600">EMS</span>
                    </span>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-slate-600 block mt-1">
                        Employee Management System
                    </span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <div class="hidden md:flex items-center space-x-8 text-sm font-semibold">
                <a href="{{ route('home') }}" class="transition-colors hover:text-brand-600 text-decoration-none {{ request()->routeIs('home') ? 'text-brand-600 font-bold' : 'text-slate-600' }}">
                    Home
                </a>
                <a href="{{ route('privacy-policy') }}" class="transition-colors hover:text-brand-600 text-decoration-none {{ request()->routeIs('privacy-policy') ? 'text-brand-600 font-bold' : 'text-slate-600' }}">
                    Privacy Policy
                </a>
                <a href="{{ route('terms') }}" class="transition-colors hover:text-brand-600 text-decoration-none {{ request()->routeIs('terms') ? 'text-brand-600 font-bold' : 'text-slate-600' }}">
                    Terms & Conditions
                </a>
                <a href="{{ route('delete-account') }}" class="transition-colors hover:text-brand-600 text-decoration-none {{ request()->routeIs('delete-account') ? 'text-brand-600 font-bold' : 'text-slate-600' }}">
                    Delete Account
                </a>
            </div>

            <!-- Action Buttons -->
            <div class="hidden sm:flex items-center space-x-2">
                <a href="{{ route('tl.login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-300 transition-all shadow-sm text-decoration-none">
                    <span class="material-symbols-outlined text-sm sm:text-base text-amber-600">crown</span>
                    TL Login
                </a>
                <a href="{{ route('sales.login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition-all shadow-sm text-decoration-none">
                    <span class="material-symbols-outlined text-sm sm:text-base text-emerald-600">storefront</span>
                    Sale Login
                </a>
                <a href="{{ route('admin.login') }}" class="btn bg-brand-600 text-white px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs sm:text-sm font-semibold hover:bg-brand-700 transition-all shadow-md shadow-brand-600/20 border-0 inline-flex items-center gap-1 text-decoration-none">
                    <span class="material-symbols-outlined text-sm sm:text-base">shield_person</span>
                    Admin Login
                </a>
            </div>

            <!-- Mobile Menu Toggle -->
            <button type="button" onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-brand-50 hover:text-brand-600 focus:outline-none" aria-label="Toggle menu">
                <span class="material-symbols-outlined text-2xl" id="menu-icon">menu</span>
            </button>
        </nav>

        <!-- Mobile Drawer Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-brand-100 bg-white px-4 pt-4 pb-6 space-y-3">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-semibold text-decoration-none {{ request()->routeIs('home') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                Home
            </a>
            <a href="{{ route('privacy-policy') }}" class="block px-3 py-2 rounded-lg text-base font-semibold text-decoration-none {{ request()->routeIs('privacy-policy') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                Privacy Policy
            </a>
            <a href="{{ route('terms') }}" class="block px-3 py-2 rounded-lg text-base font-semibold text-decoration-none {{ request()->routeIs('terms') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                Terms & Conditions
            </a>
            <a href="{{ route('delete-account') }}" class="block px-3 py-2 rounded-lg text-base font-semibold text-decoration-none {{ request()->routeIs('delete-account') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                Delete Account Request
            </a>
            <div class="pt-4 border-t border-slate-100 flex flex-col space-y-2">
                <a href="{{ route('tl.login') }}" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold bg-amber-50 text-amber-900 border border-amber-200 hover:bg-amber-100 transition-colors text-decoration-none">
                    <span class="material-symbols-outlined text-lg text-amber-600">crown</span>
                    TL (Team Leader) Login
                </a>
                <a href="{{ route('sales.login') }}" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold bg-emerald-50 text-emerald-900 border border-emerald-200 hover:bg-emerald-100 transition-colors text-decoration-none">
                    <span class="material-symbols-outlined text-lg text-emerald-600">storefront</span>
                    Sale Team Login
                </a>
                <a href="{{ route('admin.login') }}" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold bg-brand-600 text-white shadow-md text-decoration-none">
                    <span class="material-symbols-outlined text-lg">shield_person</span>
                    Admin Portal Login
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t-4 border-brand-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Column 1: Company Info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-brand-500 flex items-center justify-center text-white font-bold">
                            <span class="material-symbols-outlined">badge</span>
                        </div>
                        <span class="text-xl font-extrabold text-white font-heading tracking-tight">
                            DLK <span class="text-brand-400">EMS</span>
                        </span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Empowering organizations with unified employee records, automated payroll processing, seamless leave approvals, and intelligent timesheet management.
                    </p>
                    <div class="flex items-center space-x-3 pt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-brand-950 text-brand-400 border border-brand-800/50">
                            <span class="w-2 h-2 rounded-full bg-brand-400 animate-ping mr-2"></span>
                            System Online v2.4
                        </span>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div>
                    <h3 class="text-white font-heading font-bold text-base tracking-wider uppercase mb-4 text-xs text-brand-400">
                        Quick Navigation
                    </h3>
                    <ul class="space-y-2.5 text-sm list-none p-0">
                        <li>
                            <a href="{{ route('home') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none flex items-center">
                                <span class="material-symbols-outlined text-xs mr-2 text-brand-500">chevron_right</span>
                                Home Overview
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tl.login') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none flex items-center">
                                <span class="material-symbols-outlined text-xs mr-2 text-amber-400">chevron_right</span>
                                Team Leader (TL) Hub
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sales.login') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none flex items-center">
                                <span class="material-symbols-outlined text-xs mr-2 text-emerald-400">chevron_right</span>
                                Sales Team Portal
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.login') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none flex items-center">
                                <span class="material-symbols-outlined text-xs mr-2 text-brand-500">chevron_right</span>
                                Admin Management Dashboard
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Legal & Governance -->
                <div>
                    <h3 class="text-white font-heading font-bold text-base tracking-wider uppercase mb-4 text-xs text-brand-400">
                        Privacy & Compliance
                    </h3>
                    <ul class="space-y-2.5 text-sm list-none p-0">
                        <li>
                            <a href="{{ route('privacy-policy') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none flex items-center">
                                <span class="material-symbols-outlined text-xs mr-2 text-brand-500">shield</span>
                                Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('terms') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none flex items-center">
                                <span class="material-symbols-outlined text-xs mr-2 text-brand-500">gavel</span>
                                Terms & Conditions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('delete-account') }}" class="text-slate-400 hover:text-white transition-colors text-decoration-none flex items-center">
                                <span class="material-symbols-outlined text-xs mr-2 text-brand-500">no_accounts</span>
                                Account Deletion Details
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Contact & Help -->
                <div>
                    <h3 class="text-white font-heading font-bold text-base tracking-wider uppercase mb-4 text-xs text-brand-400">
                        Corporate Contact
                    </h3>
                    <div class="space-y-3 text-sm text-slate-400">
                        <div class="flex items-start space-x-3">
                            <span class="material-symbols-outlined text-brand-400 text-lg mt-0.5">location_on</span>
                            <span>DLK Technologies, Corporate HR Division, Tech Hub Tower.</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="material-symbols-outlined text-brand-400 text-lg">mail</span>
                            <a href="mailto:support@dlktechnologies.com" class="text-slate-400 hover:text-brand-300 text-decoration-none">
                                support@dlktechnologies.com
                            </a>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="material-symbols-outlined text-brand-400 text-lg">lock</span>
                            <span>AES-256 Encrypted Infrastructure</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 space-y-4 sm:space-y-0">
                <p>&copy; {{ date('Y') }} DLK Technologies. All rights reserved. Built with Green & White UI system.</p>
                <div class="flex space-x-6">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-slate-400 text-decoration-none">Privacy</a>
                    <a href="{{ route('terms') }}" class="hover:text-slate-400 text-decoration-none">Terms</a>
                    <a href="{{ route('delete-account') }}" class="hover:text-slate-400 text-decoration-none">Delete Account</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.textContent = 'close';
            } else {
                menu.classList.add('hidden');
                icon.textContent = 'menu';
            }
        }
    </script>
    @stack('scripts')
</body>

</html>

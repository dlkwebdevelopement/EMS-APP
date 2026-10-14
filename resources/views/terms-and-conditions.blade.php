@extends('layouts.public')

@section('title', 'Terms & Conditions - DLK EMS')
@section('meta_description', 'Terms and conditions for accessing and utilizing the DLK Employee Management System.')

@section('content')
<!-- Header Banner -->
<section class="bg-green-soft py-12 md:py-16 border-b border-brand-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-brand-100 text-brand-800 text-xs font-bold uppercase tracking-wider mb-4">
                <span class="material-symbols-outlined text-sm">gavel</span>
                System Governance & Operating Rules
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight font-heading mb-4">
                Terms & Conditions
            </h1>
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                Rules, guidelines, user obligations, and software licensing terms governing access to the <span class="font-bold text-slate-800">DLK Employee Management System</span>.
            </p>
            <div class="mt-6 flex flex-wrap items-center gap-6 text-xs text-slate-500 font-medium">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-brand-600 text-sm mr-1">history</span>
                    Effective Date: July 27, 2026
                </span>
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-brand-600 text-sm mr-1">check_circle</span>
                    Version 3.1 Corporate Standard
                </span>
            </div>
        </div>
    </div>
</section>

<!-- Content Body -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            <!-- Sidebar Navigation -->
            <div class="lg:col-span-4">
                <div class="sticky top-28 bg-slate-50 rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-xs font-extrabold text-brand-600 uppercase tracking-widest border-b border-slate-200 pb-3">
                        Terms Navigation
                    </h3>
                    <nav class="space-y-2 text-sm font-medium">
                        <a href="#acceptance" class="block text-slate-700 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-brand-500 pl-3">
                            1. Acceptance of Terms
                        </a>
                        <a href="#account-security" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            2. Account Security & Credentials
                        </a>
                        <a href="#intellectual-property" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            3. Software & IP Rights
                        </a>
                        <a href="#acceptable-use" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            4. Acceptable Conduct & Misuse
                        </a>
                        <a href="#service-availability" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            5. System Availability (SLA)
                        </a>
                        <a href="#liability" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            6. Limitation of Liability
                        </a>
                        <a href="#termination" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            7. Account Deletion & Termination
                        </a>
                    </nav>

                    <div class="pt-4 border-t border-slate-200 text-xs">
                        <div class="p-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-600">
                            <strong>Need support?</strong> Contact your organization administrator or write to <span class="text-brand-600 font-semibold">support@dlktechnologies.com</span>.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Legal Content -->
            <div class="lg:col-span-8 space-y-10 text-slate-700 leading-relaxed">

                <!-- Section 1 -->
                <div id="acceptance" class="space-y-4 scroll-mt-28">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">1</span>
                        Acceptance of Terms
                    </h2>
                    <p>
                        By accessing, logging into, or submitting information through the DLK Employee Management System (EMS), you agree to be bound by these Terms and Conditions ("Terms"). If you are accessing this portal on behalf of an employer or enterprise entity, you represent that you possess the requisite authority to bind that entity.
                    </p>
                </div>

                <!-- Section 2 -->
                <div id="account-security" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">2</span>
                        Account Credentials & Security
                    </h2>
                    <p>
                        Authorized users are issued individual user credentials (email address and confidential password) to log into employee or administrative portals.
                    </p>
                    <ul class="space-y-2 text-sm list-disc pl-5">
                        <li>You are strictly prohibited from sharing login credentials, session tokens, or one-time passcodes with third parties.</li>
                        <li>You must notify your IT/HR administrator immediately if you suspect unauthorized access or credential compromise.</li>
                        <li>Automated account lockouts occur after consecutive failed login attempts to prevent brute-force intrusion.</li>
                    </ul>
                </div>

                <!-- Section 3 -->
                <div id="intellectual-property" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">3</span>
                        Software & Intellectual Property
                    </h2>
                    <p>
                        All source code, user interfaces, branding assets, database architecture, algorithms, and documentation comprising the DLK EMS system remain the exclusive intellectual property of <strong>DLK Technologies</strong>. No license is granted to decompile, reverse-engineer, copy, or redistribute any part of this software suite.
                    </p>
                </div>

                <!-- Section 4 -->
                <div id="acceptable-use" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">4</span>
                        Acceptable Conduct & Prohibited Misuse
                    </h2>
                    <p>
                        When using the platform for daily attendance check-ins, timesheet submission, or administrative approvals, users must refrain from:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-4">
                        <div class="p-4 rounded-xl bg-red-50/60 border border-red-100 text-xs space-y-1">
                            <span class="font-bold text-red-800 flex items-center">
                                <span class="material-symbols-outlined text-sm mr-1">block</span>
                                Attendance Falsification
                            </span>
                            <p class="text-red-700 mb-0">Proxy check-ins, spoofed location coordinates, or unauthorized attendance manipulation.</p>
                        </div>

                        <div class="p-4 rounded-xl bg-red-50/60 border border-red-100 text-xs space-y-1">
                            <span class="font-bold text-red-800 flex items-center">
                                <span class="material-symbols-outlined text-sm mr-1">block</span>
                                Unauthorized Data Scraping
                            </span>
                            <p class="text-red-700 mb-0">Automated scraping of colleague records, compensation data, or system assets.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 5 -->
                <div id="service-availability" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">5</span>
                        System Availability & Service Level
                    </h2>
                    <p>
                        While we strive for continuous 99.99% system uptime, planned maintenance windows may occasionally occur. Administrators will publish advance notifications prior to major system updates or database index optimizations.
                    </p>
                </div>

                <!-- Section 6 -->
                <div id="liability" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">6</span>
                        Limitation of Liability
                    </h2>
                    <p>
                        To the maximum extent permitted by applicable law, DLK Technologies shall not be liable for indirect, incidental, or consequential damages resulting from user connectivity issues, unauthorized third-party device compromise, or user error in timesheet input.
                    </p>
                </div>

                <!-- Section 7 -->
                <div id="termination" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">7</span>
                        Account Termination & Deletion
                    </h2>
                    <p>
                        Accounts may be deactivated upon employee resignation, contract completion, or administrative policy enforcement. For details on how user records are purged or archived, please refer to our <a href="{{ route('delete-account') }}" class="text-brand-600 font-bold underline">Delete Account Details Page</a>.
                    </p>
                </div>

            </div>

        </div>
    </div>
</section>
@endsection

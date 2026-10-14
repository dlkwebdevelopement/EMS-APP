@extends('layouts.public')

@section('title', 'DLK EMS - Modern Employee Management System')

@section('content')
<!-- Hero Section -->
<section class="relative pt-12 pb-20 md:pt-20 md:pb-32 overflow-hidden bg-green-soft">
    <!-- Ambient Green Backdrop Circles -->
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-brand-200/40 rounded-full blur-3xl -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-10 w-80 h-80 bg-brand-300/30 rounded-full blur-3xl -z-10 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Hero Column -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-brand-100/80 border border-brand-200 text-brand-800 text-xs sm:text-sm font-semibold tracking-wide shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-600 animate-pulse"></span>
                    <span>Enterprise HR & Workplace Operations Portal</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                    Streamline Your Workforce & <span class="text-gradient-green">Empower Your Team</span>
                </h1>

                <p class="text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0 font-normal">
                    An all-in-one management platform designed for modern enterprises. Track daily attendance, process payroll automatically, approve leave requests, and manage timesheets with maximum security and ease.
                </p>

                <!-- Hero Actions -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 pt-2">
                    <a href="{{ route('admin.login') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-brand-600 text-white font-semibold text-sm sm:text-base shadow-lg shadow-brand-600/30 hover:bg-brand-700 hover:shadow-brand-600/40 transition-all flex items-center justify-center text-decoration-none">
                        <span class="material-symbols-outlined mr-2 text-lg">admin_panel_settings</span>
                        Admin Dashboard
                    </a>
                    <a href="{{ route('tl.login') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-amber-500 text-white font-semibold text-sm sm:text-base shadow-lg shadow-amber-500/25 hover:bg-amber-600 hover:shadow-amber-500/35 transition-all flex items-center justify-center text-decoration-none">
                        <span class="material-symbols-outlined mr-2 text-lg">crown</span>
                        Team Leader (TL)
                    </a>
                    <a href="{{ route('sales.login') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold text-sm sm:text-base shadow-lg shadow-emerald-600/25 hover:bg-emerald-700 hover:shadow-emerald-600/35 transition-all flex items-center justify-center text-decoration-none">
                        <span class="material-symbols-outlined mr-2 text-lg">storefront</span>
                        Sales Portal
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="pt-6 border-t border-brand-200/60 flex items-center justify-center lg:justify-start space-x-6 text-slate-500 text-xs font-medium">
                    <span class="flex items-center">
                        <span class="material-symbols-outlined text-brand-600 text-sm mr-1">verified_user</span>
                        256-Bit Encrypted
                    </span>
                    <span class="flex items-center">
                        <span class="material-symbols-outlined text-brand-600 text-sm mr-1">speed</span>
                        Instant Payroll
                    </span>
                    <span class="flex items-center">
                        <span class="material-symbols-outlined text-brand-600 text-sm mr-1">thumb_up</span>
                        GDPR Compliant
                    </span>
                </div>
            </div>

            <!-- Right Hero Visual Card -->
            <div class="lg:col-span-5">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-brand-100 relative z-10 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-brand-500 text-white flex items-center justify-center font-bold">
                                    EMS
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-base mb-0">System Status</h4>
                                    <span class="text-xs text-brand-600 font-semibold">Live Operational Sync</span>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-brand-100 text-brand-800 text-xs font-bold">Active</span>
                        </div>

                        <!-- Mini Stats Widgets -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                <span class="text-xs text-slate-500 font-medium block">Total Workforce</span>
                                <span class="text-2xl font-extrabold text-slate-900 font-mono block mt-1">1,240+</span>
                                <span class="text-[11px] text-brand-600 font-semibold flex items-center mt-1">
                                    <span class="material-symbols-outlined text-xs mr-0.5">trending_up</span> +12% this month
                                </span>
                            </div>
                            <div class="bg-brand-50 rounded-2xl p-4 border border-brand-100">
                                <span class="text-xs text-brand-800 font-medium block">Today's Check-Ins</span>
                                <span class="text-2xl font-extrabold text-brand-700 font-mono block mt-1">98.4%</span>
                                <span class="text-[11px] text-brand-700 font-semibold flex items-center mt-1">
                                    <span class="material-symbols-outlined text-xs mr-0.5">check_circle</span> On-time status
                                </span>
                            </div>
                        </div>

                        <!-- Sample Activity List -->
                        <div class="space-y-3">
                            <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400">Recent System Activity</h5>
                            
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 text-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-base">payments</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 mb-0">Monthly Payroll Generated</p>
                                        <p class="text-slate-400 mb-0">All departments processed</p>
                                    </div>
                                </div>
                                <span class="text-slate-400 font-mono">Just now</span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 text-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-base">event_available</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 mb-0">Leave Approved</p>
                                        <p class="text-slate-400 mb-0">Engineering Team</p>
                                    </div>
                                </div>
                                <span class="text-slate-400 font-mono">10m ago</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Feature Showcase Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
            <h2 class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-4 py-1.5 rounded-full inline-block">
                Core Modules & Features
            </h2>
            <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Everything You Need for Complete Workforce Control
            </h3>
            <p class="text-slate-600 text-base">
                Built with precision to replace tedious manual spreadsheets with automated, compliant software workflows.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Feature 1 -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 green-card-hover relative group">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-6 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">schedule</span>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Attendance & Check-in</h4>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Real-time daily check-in and check-out tracking with geo-location verification, automated late penalty logs, and monthly summary exports.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 green-card-hover relative group">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-6 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">payments</span>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Automated Payroll</h4>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Generate instant payslips with Provident Fund (PF), tax deductions, allowances, bonus computations, and PDF payslip downloads.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 green-card-hover relative group">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-6 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">event_busy</span>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Leave & Time-Off</h4>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Streamlined leave applications, multi-tier admin approvals, real-time balance calculations, and holiday calendar integration.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 green-card-hover relative group">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-6 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">view_timeline</span>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Timesheet Tracking</h4>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Log project hours, track productivity metrics across departments, submit weekly timesheets, and export comprehensive client reports.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 green-card-hover relative group">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-6 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">devices_other</span>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Asset Allocation</h4>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Manage company hardware, laptops, components, repair histories, maintenance invoices, and digital handover receipts.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 green-card-hover relative group">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-6 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl">shield_lock</span>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Security & Compliance</h4>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Granular role-based access control (RBAC), activity audit logs, AES-256 encrypted employee data, and full data deletion workflows.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- Metrics & Performance Banner -->
<section class="py-16 bg-slate-900 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            
            <div class="space-y-2">
                <div class="text-4xl sm:text-5xl font-extrabold text-brand-400 font-mono">99.99%</div>
                <div class="text-xs sm:text-sm font-medium text-slate-400 uppercase tracking-wider">System Uptime</div>
            </div>

            <div class="space-y-2">
                <div class="text-4xl sm:text-5xl font-extrabold text-brand-400 font-mono">50K+</div>
                <div class="text-xs sm:text-sm font-medium text-slate-400 uppercase tracking-wider">Payslips Generated</div>
            </div>

            <div class="space-y-2">
                <div class="text-4xl sm:text-5xl font-extrabold text-brand-400 font-mono">100%</div>
                <div class="text-xs sm:text-sm font-medium text-slate-400 uppercase tracking-wider">Automated Audit Log</div>
            </div>

            <div class="space-y-2">
                <div class="text-4xl sm:text-5xl font-extrabold text-brand-400 font-mono">&lt; 1 sec</div>
                <div class="text-xs sm:text-sm font-medium text-slate-400 uppercase tracking-wider">Check-in Latency</div>
            </div>

        </div>
    </div>
</section>

<!-- Interactive FAQ Section -->
<section class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-12 space-y-3">
            <h2 class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Frequently Asked Questions</h2>
            <h3 class="text-3xl font-extrabold text-slate-900">Have Questions? We Have Answers</h3>
        </div>

        <div class="space-y-4">
            
            <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm">
                <h4 class="text-lg font-bold text-slate-900 mb-2 flex items-center">
                    <span class="material-symbols-outlined text-brand-600 mr-2">help_outline</span>
                    How do employees access their monthly payslips?
                </h4>
                <p class="text-slate-600 text-sm leading-relaxed mb-0 pl-8">
                    Employees can log into the Employee Portal using their credentials, navigate to the "Payslips" section, and view or download PDF copies of their monthly payslips at any time.
                </p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm">
                <h4 class="text-lg font-bold text-slate-900 mb-2 flex items-center">
                    <span class="material-symbols-outlined text-brand-600 mr-2">help_outline</span>
                    What is the policy regarding account deletion?
                </h4>
                <p class="text-slate-600 text-sm leading-relaxed mb-0 pl-8">
                    We provide transparent account deletion options for former employees or organizational accounts. Visit our dedicated <a href="{{ route('delete-account') }}" class="text-brand-600 font-semibold underline">Delete Account Details Page</a> to submit a deletion request or review data retention timelines.
                </p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm">
                <h4 class="text-lg font-bold text-slate-900 mb-2 flex items-center">
                    <span class="material-symbols-outlined text-brand-600 mr-2">help_outline</span>
                    Is my employee personal data secure?
                </h4>
                <p class="text-slate-600 text-sm leading-relaxed mb-0 pl-8">
                    Yes. All database connections and file storage use enterprise-grade AES-256 encryption. We comply strictly with data privacy regulations detailed in our <a href="{{ route('privacy-policy') }}" class="text-brand-600 font-semibold underline">Privacy Policy</a>.
                </p>
            </div>

        </div>

    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-16 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-green-gradient rounded-3xl p-8 sm:p-12 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-3 text-center md:text-left">
                <h3 class="text-2xl sm:text-3xl font-extrabold">Ready to access your EMS Portal?</h3>
                <p class="text-brand-100 text-base max-w-xl">
                    Log in now to manage your daily attendance, apply for leaves, or oversee organizational operations.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                <a href="{{ route('employee.login') }}" class="btn bg-white text-brand-800 hover:bg-brand-50 px-6 py-3 rounded-xl font-bold text-decoration-none text-center shadow-md">
                    Employee Login
                </a>
                <a href="{{ route('admin.login') }}" class="btn bg-brand-900 text-white hover:bg-brand-950 px-6 py-3 rounded-xl font-bold text-decoration-none text-center shadow-md border border-brand-700">
                    Admin Login
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
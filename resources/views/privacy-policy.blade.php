@extends('layouts.public')

@section('title', 'Privacy Policy - DLK EMS')
@section('meta_description', 'Privacy policy and data protection guidelines for DLK Employee Management System users and employees.')

@section('content')
<!-- Header Banner -->
<section class="bg-green-soft py-12 md:py-16 border-b border-brand-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-brand-100 text-brand-800 text-xs font-bold uppercase tracking-wider mb-4">
                <span class="material-symbols-outlined text-sm">shield</span>
                Data Protection & Privacy Policy
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight font-heading mb-4">
                Privacy Policy
            </h1>
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                At <span class="font-bold text-slate-800">DLK Technologies</span>, we are committed to safeguarding your personal information, employee records, and payroll data with enterprise-grade security standards.
            </p>
            <div class="mt-6 flex flex-wrap items-center gap-6 text-xs text-slate-500 font-medium">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-brand-600 text-sm mr-1">calendar_today</span>
                    Last Updated: July 27, 2026
                </span>
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-brand-600 text-sm mr-1">verified</span>
                    Version 2.4 Active Policy
                </span>
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-brand-600 text-sm mr-1">lock</span>
                    AES-256 Encryption Mandate
                </span>
            </div>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            <!-- Sidebar Sticky Table of Contents -->
            <div class="lg:col-span-4">
                <div class="sticky top-28 bg-slate-50 rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-xs font-extrabold text-brand-600 uppercase tracking-widest border-b border-slate-200 pb-3">
                        On This Page
                    </h3>
                    <nav class="space-y-2 text-sm font-medium">
                        <a href="#overview" class="block text-slate-700 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-brand-500 pl-3">
                            1. System Overview & Scope
                        </a>
                        <a href="#data-collection" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            2. Data We Collect
                        </a>
                        <a href="#data-usage" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            3. Purpose & Data Usage
                        </a>
                        <a href="#data-security" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            4. Security & Storage Policies
                        </a>
                        <a href="#employee-rights" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            5. Employee Data Rights
                        </a>
                        <a href="#deletion-policy" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            6. Account Deletion & Retention
                        </a>
                        <a href="#contact-dpo" class="block text-slate-600 hover:text-brand-600 transition-colors text-decoration-none py-1.5 border-l-2 border-transparent hover:border-brand-300 pl-3">
                            7. Data Officer Contact
                        </a>
                    </nav>

                    <div class="pt-4 border-t border-slate-200">
                        <div class="bg-brand-50 rounded-xl p-4 border border-brand-100 text-xs text-brand-900 space-y-2">
                            <span class="font-bold block">Looking to delete your account?</span>
                            <p class="text-brand-800 text-[11px] leading-normal mb-2">
                                Review our step-by-step account deletion procedures and request form.
                            </p>
                            <a href="{{ route('delete-account') }}" class="btn bg-brand-600 text-white btn-sm rounded-lg font-bold text-xs w-full">
                                Account Deletion Page
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Legal Body -->
            <div class="lg:col-span-8 space-y-12 text-slate-700 leading-relaxed">
                
                <!-- Section 1 -->
                <div id="overview" class="space-y-4 scroll-mt-28">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">1</span>
                        System Overview & Scope
                    </h2>
                    <p>
                        This Privacy Policy outlines how DLK Technologies ("Company", "we", "our", or "us") collects, uses, processes, and protects personal data obtained through the DLK Employee Management System (EMS). This policy applies to all current employees, former employees, job applicants, contractors, and platform administrators who access our application portal at <code class="bg-slate-100 px-2 py-0.5 rounded text-xs text-brand-700 font-mono">/employee/login</code> or <code class="bg-slate-100 px-2 py-0.5 rounded text-xs text-brand-700 font-mono">/admin/login</code>.
                    </p>
                    <p>
                        By logging into and utilizing the EMS system, you acknowledge that your personal and operational workplace data will be processed in accordance with this policy and applicable labor laws.
                    </p>
                </div>

                <!-- Section 2 -->
                <div id="data-collection" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">2</span>
                        Information We Collect
                    </h2>
                    <p>
                        We collect and maintain only the information strictly necessary for statutory employment compliance, payroll generation, performance evaluation, and workplace security:
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                            <h4 class="font-bold text-slate-900 text-sm mb-2 flex items-center">
                                <span class="material-symbols-outlined text-brand-600 mr-2">badge</span>
                                Personal Profile Information
                            </h4>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 mb-0">
                                <li>Full Name, Gender, Date of Birth</li>
                                <li>Official & Personal Email Addresses</li>
                                <li>Phone Numbers & Emergency Contact Details</li>
                                <li>Residential Address & ID Proof Documents</li>
                            </ul>
                        </div>

                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                            <h4 class="font-bold text-slate-900 text-sm mb-2 flex items-center">
                                <span class="material-symbols-outlined text-brand-600 mr-2">account_balance</span>
                                Financial & Payroll Data
                            </h4>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 mb-0">
                                <li>Bank Account Numbers & IFSC Codes</li>
                                <li>Provident Fund (PF) & UAN Identifiers</li>
                                <li>Base Salary, Allowances, Bonus Records</li>
                                <li>Tax Deductions (TDS) & Income Statements</li>
                            </ul>
                        </div>

                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                            <h4 class="font-bold text-slate-900 text-sm mb-2 flex items-center">
                                <span class="material-symbols-outlined text-brand-600 mr-2">schedule</span>
                                Work & Attendance Logs
                            </h4>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 mb-0">
                                <li>Daily Check-in / Check-out timestamps</li>
                                <li>Geo-location IP markers during punch-in</li>
                                <li>Leave Applications & Absence Reason Records</li>
                                <li>Weekly Timesheet Hours & Project Logs</li>
                            </ul>
                        </div>

                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                            <h4 class="font-bold text-slate-900 text-sm mb-2 flex items-center">
                                <span class="material-symbols-outlined text-brand-600 mr-2">computer</span>
                                System & Asset Audit Data
                            </h4>
                            <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 mb-0">
                                <li>Assigned Laptop/Hardware Serial Numbers</li>
                                <li>Portal Login Timestamps & IP Logs</li>
                                <li>Browser User-Agent Strings for Security</li>
                                <li>Document Upload Verification Logs</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div id="data-usage" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">3</span>
                        Purpose & Data Usage
                    </h2>
                    <p>
                        Your data is processed strictly for administrative, statutory, and operational workplace needs. Specifically:
                    </p>
                    <ul class="space-y-2 list-disc pl-5 text-sm">
                        <li><strong>Payroll Disbursement:</strong> Calculating monthly salaries, generating PDF payslips, and executing direct bank wire transfers.</li>
                        <li><strong>Attendance & Leave Management:</strong> Monitoring shift compliance, calculating leave balances, and approving time-off requests.</li>
                        <li><strong>Statutory Compliance:</strong> Remitting Provident Fund (PF) deposits, employee state insurance, and filing income tax returns with regulatory bodies.</li>
                        <li><strong>Asset Tracking:</strong> Maintaining asset allocation, repair maintenance invoices, and hardware handover receipts.</li>
                    </ul>
                </div>

                <!-- Section 4 -->
                <div id="data-security" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">4</span>
                        Security & Encryption Standards
                    </h2>
                    <div class="bg-brand-50 border border-brand-200 rounded-2xl p-6 space-y-3">
                        <div class="flex items-center space-x-2 text-brand-800 font-bold text-base">
                            <span class="material-symbols-outlined text-brand-600">verified_user</span>
                            <span>Enterprise Security Infrastructure</span>
                        </div>
                        <p class="text-xs text-brand-900 leading-relaxed">
                            We implement stringent technical measures including TLS 1.3 encryption in transit, AES-256 bit encryption at rest for sensitive financial identifiers, automated database backup rotation, and strict Role-Based Access Control (RBAC) preventing unauthorized access across departments.
                        </p>
                    </div>
                </div>

                <!-- Section 5 -->
                <div id="employee-rights" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">5</span>
                        Employee Data Rights
                    </h2>
                    <p>
                        As an employee or user of the system, you retain rights under applicable privacy frameworks:
                    </p>
                    <ul class="space-y-2 text-sm list-disc pl-5">
                        <li><strong>Right to Access:</strong> Inspect your personal profile data and download historical payslips via your portal account.</li>
                        <li><strong>Right to Rectification:</strong> Request correction of inaccurate bank details, emergency contacts, or personal details through HR administrators.</li>
                        <li><strong>Right to Restrict Processing:</strong> Request restrictions on non-statutory data processing upon leaving the organization.</li>
                    </ul>
                </div>

                <!-- Section 6 -->
                <div id="deletion-policy" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">6</span>
                        Account Deletion & Data Retention
                    </h2>
                    <p>
                        When an employee resigns or their contract terminates, the user account undergoes a structured lifecycle transition. Primary login credentials are deactivated, while financial and tax compliance records are retained for statutory periods mandated by law (typically 7 years).
                    </p>
                    <p>
                        For complete step-by-step instructions on submitting account deletion requests or purging personal files, please visit our <a href="{{ route('delete-account') }}" class="text-brand-600 font-bold underline">Delete Account Details Page</a>.
                    </p>
                </div>

                <!-- Section 7 -->
                <div id="contact-dpo" class="space-y-4 scroll-mt-28 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold mr-3">7</span>
                        Data Protection Officer Contact
                    </h2>
                    <p>
                        If you have questions regarding this Privacy Policy or wish to exercise your data rights, please contact our Data Governance team:
                    </p>

                    <div class="bg-slate-900 text-white rounded-2xl p-6 space-y-3">
                        <h4 class="font-bold text-base text-brand-400">DLK Technologies Data Protection Division</h4>
                        <div class="text-xs text-slate-300 space-y-1 font-mono">
                            <p class="mb-0">Email: privacy@dlktechnologies.com</p>
                            <p class="mb-0">Phone: +91 44 2345 6789 (Mon - Fri, 9 AM - 6 PM IST)</p>
                            <p class="mb-0">Address: Tech Hub Tower, Corporate Legal Dept, Chennai, India</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>
@endsection

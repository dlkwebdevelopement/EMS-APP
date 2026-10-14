@extends('layouts.public')

@section('title', 'Delete Account Details - DLK EMS')
@section('meta_description', 'Detailed guidelines, step-by-step instructions, and deletion request procedures for DLK Employee Management System accounts.')

@section('content')
<!-- Header Banner -->
<section class="bg-green-soft py-12 md:py-16 border-b border-brand-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-bold uppercase tracking-wider mb-4">
                <span class="material-symbols-outlined text-sm">no_accounts</span>
                Account Lifecycle & Data Purge Policy
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight font-heading mb-4">
                Account Deletion Details
            </h1>
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                Clear, transparent guidelines on how to request account deletion, data retention windows, asset handover obligations, and automated profile purging in <span class="font-bold text-slate-800">DLK EMS</span>.
            </p>
            
            <div class="mt-6 flex flex-wrap items-center gap-4">
                <button type="button" data-bs-toggle="modal" data-bs-target="#deleteRequestModal" class="btn bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md border-0 flex items-center">
                    <span class="material-symbols-outlined mr-2 text-base">delete_forever</span>
                    Submit Account Deletion Request
                </button>
                <a href="#deletion-steps" class="btn btn-outline-secondary px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 border-slate-300">
                    Read Deletion Steps Below
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Main Details Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Left 8 Columns: Process & Breakdown -->
            <div class="lg:col-span-8 space-y-12">
                
                <!-- Overview Alert -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 flex items-start space-x-4">
                    <span class="material-symbols-outlined text-amber-600 text-3xl mt-0.5">warning</span>
                    <div class="space-y-1 text-sm text-amber-900">
                        <h4 class="font-bold text-amber-950 text-base mb-1">Important Notice Before Proceeding</h4>
                        <p class="mb-0 leading-relaxed">
                            Deleting your account is a permanent action regarding your system login access. Once processed by HR Administrators, active portal credentials will be revoked. Make sure to download all your payslips and tax documents prior to submitting a deletion request.
                        </p>
                    </div>
                </div>

                <!-- Step-by-Step Guide -->
                <div id="deletion-steps" class="space-y-6">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading">
                        Step-by-Step Account Deletion Process
                    </h2>

                    <div class="space-y-4">
                        
                        <!-- Step 1 -->
                        <div class="flex items-start space-x-4 bg-slate-50 p-6 rounded-2xl border border-slate-200 green-card-hover">
                            <div class="w-10 h-10 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center flex-shrink-0 text-base">
                                1
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-slate-900">Download Required Employee Documents</h3>
                                <p class="text-slate-600 text-sm leading-relaxed mb-0">
                                    Log into your <a href="{{ route('employee.login') }}" class="text-brand-600 font-semibold underline">Employee Portal</a> and download your historical monthly payslips, Form-16 tax records, and experience certificates for your personal records.
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex items-start space-x-4 bg-slate-50 p-6 rounded-2xl border border-slate-200 green-card-hover">
                            <div class="w-10 h-10 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center flex-shrink-0 text-base">
                                2
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-slate-900">Clear Asset & Hardware Handover</h3>
                                <p class="text-slate-600 text-sm leading-relaxed mb-0">
                                    Ensure all allocated company hardware (laptops, identity access cards, mobile devices) have been returned to IT & Asset Management with signed clearance receipts.
                                </p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex items-start space-x-4 bg-slate-50 p-6 rounded-2xl border border-slate-200 green-card-hover">
                            <div class="w-10 h-10 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center flex-shrink-0 text-base">
                                3
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-slate-900">Submit Account Deletion Request</h3>
                                <p class="text-slate-600 text-sm leading-relaxed mb-0">
                                    Click the <strong>"Submit Deletion Request"</strong> button to fill out the digital request form, providing your Employee ID, registered email, and reason for account termination.
                                </p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="flex items-start space-x-4 bg-slate-50 p-6 rounded-2xl border border-slate-200 green-card-hover">
                            <div class="w-10 h-10 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center flex-shrink-0 text-base">
                                4
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-slate-900">Admin Review & Data Purge</h3>
                                <p class="text-slate-600 text-sm leading-relaxed mb-0">
                                    HR and IT administrators review the request within 3-5 business days. Once verified, active credentials are destroyed, non-statutory data is purged, and a confirmation email is dispatched.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Data Retention & Purge Summary Table -->
                <div class="space-y-4 pt-6 border-t border-slate-100">
                    <h2 class="text-2xl font-bold text-slate-900 font-heading">
                        Data Retention & Purging Schedule
                    </h2>
                    <p class="text-slate-600 text-sm">
                        Below is a detailed breakdown of how different categories of your account data are handled upon account deletion:
                    </p>

                    <div class="overflow-x-auto border border-slate-200 rounded-2xl shadow-sm">
                        <table class="table table-hover align-middle mb-0 text-sm">
                            <thead class="table-light text-slate-700 font-bold uppercase text-xs">
                                <tr>
                                    <th class="py-3.5 px-4">Data Category</th>
                                    <th class="py-3.5 px-4">Deletion Action</th>
                                    <th class="py-3.5 px-4">Retention Window</th>
                                    <th class="py-3.5 px-4">Legal Baseline</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                <tr>
                                    <td class="font-bold text-slate-900 py-3 px-4">Login Passwords & Session Tokens</td>
                                    <td class="py-3 px-4"><span class="badge bg-red-100 text-red-800 border border-red-200 font-semibold">Immediate Purge</span></td>
                                    <td class="py-3 px-4">0 Days</td>
                                    <td class="py-3 px-4 text-xs text-slate-500">Security Requirement</td>
                                </tr>
                                <tr>
                                    <td class="font-bold text-slate-900 py-3 px-4">Profile Emergency Contacts & Address</td>
                                    <td class="py-3 px-4"><span class="badge bg-red-100 text-red-800 border border-red-200 font-semibold">Purged</span></td>
                                    <td class="py-3 px-4">30 Days after exit</td>
                                    <td class="py-3 px-4 text-xs text-slate-500">Privacy Protection</td>
                                </tr>
                                <tr>
                                    <td class="font-bold text-slate-900 py-3 px-4">Attendance & Punch Logs</td>
                                    <td class="py-3 px-4"><span class="badge bg-amber-100 text-amber-800 border border-amber-200 font-semibold">Archived</span></td>
                                    <td class="py-3 px-4">3 Years</td>
                                    <td class="py-3 px-4 text-xs text-slate-500">Labor Law Audit</td>
                                </tr>
                                <tr>
                                    <td class="font-bold text-slate-900 py-3 px-4">Payroll Statements & Tax Filings (PF/TDS)</td>
                                    <td class="py-3 px-4"><span class="badge bg-blue-100 text-blue-800 border border-blue-200 font-semibold">Statutory Vault</span></td>
                                    <td class="py-3 px-4">7 Years</td>
                                    <td class="py-3 px-4 text-xs text-slate-500">Income Tax & PF Act</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right 4 Columns: Quick Help & Launch Card -->
            <div class="lg:col-span-4 space-y-6">
                
                <div class="bg-gradient-to-br from-slate-900 to-brand-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl space-y-6 border border-slate-800">
                    <div class="w-12 h-12 rounded-2xl bg-brand-500/20 text-brand-400 flex items-center justify-center border border-brand-500/30">
                        <span class="material-symbols-outlined text-2xl">person_remove</span>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xl font-bold font-heading text-white">Ready to Delete?</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Submit a formal account deletion request. Our HR team will review your clearance and notify you by email.
                        </p>
                    </div>

                    <button type="button" data-bs-toggle="modal" data-bs-target="#deleteRequestModal" class="w-full btn bg-brand-500 hover:bg-brand-600 text-white font-bold py-3 rounded-xl shadow-lg border-0">
                        Launch Request Modal
                    </button>

                    <div class="pt-4 border-t border-slate-800/80 text-xs text-slate-400 space-y-2">
                        <p class="mb-0 flex items-center">
                            <span class="material-symbols-outlined text-brand-400 text-sm mr-2">headset_mic</span>
                            HR Desk: +91 44 2345 6789
                        </p>
                        <p class="mb-0 flex items-center">
                            <span class="material-symbols-outlined text-brand-400 text-sm mr-2">mail</span>
                            hr-support@dlktechnologies.com
                        </p>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 text-xs text-slate-600 space-y-3">
                    <h4 class="font-bold text-slate-900 text-sm mb-1 flex items-center">
                        <span class="material-symbols-outlined text-brand-600 text-base mr-1.5">help</span>
                        Frequently Asked Deletion Questions
                    </h4>
                    
                    <div class="space-y-2">
                        <p class="font-bold text-slate-800 mb-0">Q: Can I restore my account after deletion?</p>
                        <p class="text-slate-500 mb-2">A: Once credentials and personal data are purged, an account cannot be restored. Former employees returning to the company will receive a new onboarding profile.</p>

                        <p class="font-bold text-slate-800 mb-0">Q: How long does approval take?</p>
                        <p class="text-slate-500 mb-0">A: Standard processing takes 3 to 5 working days following asset clearance.</p>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

<!-- Modal: Deletion Request Form -->
<div class="modal fade" id="deleteRequestModal" tabindex="-1" aria-labelledby="deleteRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3xl border-0 shadow-2xl overflow-hidden">
            
            <div class="modal-header bg-slate-900 text-white border-0 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center border border-red-500/30">
                        <span class="material-symbols-outlined text-xl">delete_forever</span>
                    </div>
                    <h5 class="modal-title font-bold text-base font-heading" id="deleteRequestModalLabel">Account Deletion Request</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-6 space-y-4">
                <div id="deletionAlert" class="hidden alert alert-success text-xs rounded-xl border-0 mb-3" role="alert">
                    Account deletion request recorded successfully! HR will process your request within 3-5 business days.
                </div>

                <form id="deletionForm" onsubmit="handleDeletionSubmit(event)">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label text-xs font-bold uppercase tracking-wider text-slate-700">Employee ID / Code *</label>
                        <input type="text" id="employee_id" class="form-control rounded-xl text-sm py-2.5 px-3 border-slate-300" placeholder="e.g. DLK-EMP-1042" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-xs font-bold uppercase tracking-wider text-slate-700">Registered Email Address *</label>
                        <input type="email" id="email" class="form-control rounded-xl text-sm py-2.5 px-3 border-slate-300" placeholder="name@dlktechnologies.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label text-xs font-bold uppercase tracking-wider text-slate-700">Reason for Deletion *</label>
                        <select id="reason" class="form-select rounded-xl text-sm py-2.5 px-3 border-slate-300" required>
                            <option value="">-- Select Reason --</option>
                            <option value="resignation">Employment Resignation / Contract Completion</option>
                            <option value="privacy">Privacy & Data Purging Request</option>
                            <option value="duplicate">Duplicate Profile Cleanup</option>
                            <option value="other">Other Reason</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="comments" class="form-label text-xs font-bold uppercase tracking-wider text-slate-700">Additional Details / Clearance Notes</label>
                        <textarea id="comments" rows="3" class="form-control rounded-xl text-sm p-3 border-slate-300" placeholder="Provide any additional notes regarding asset clearance or exit details..."></textarea>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                        <label class="form-check-label text-xs text-slate-600" for="confirmCheck">
                            I confirm that I have downloaded all required payslips and returned all company assets prior to submitting this deletion request.
                        </label>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-2 border-t border-slate-100">
                        <button type="button" class="btn btn-light rounded-xl text-xs font-semibold px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold px-5 py-2 border-0">
                            Submit Formal Request
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function handleDeletionSubmit(event) {
        event.preventDefault();
        const alertBox = document.getElementById('deletionAlert');
        alertBox.classList.remove('hidden');
        document.getElementById('deletionForm').reset();
        
        setTimeout(() => {
            const modalEl = document.getElementById('deleteRequestModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
            alertBox.classList.add('hidden');
        }, 3000);
    }
</script>
@endpush
@endsection

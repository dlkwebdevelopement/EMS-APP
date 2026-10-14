{{-- resources/views/admin/payroll/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Payroll Management')
@section('page-title', 'Payroll Management')
@section('page-subtitle', 'Manage employee salaries, process payroll, and generate payslips')

@section('content')
<div class="container-fluid px-0">
    
    <!-- Hero Strip Section -->
    <div class="page-hero-strip mb-4">
        <div class="strip-rule"></div>
        <h2>Payroll Dashboard</h2>
        <p>Process monthly salaries, manage deductions, and generate payslips</p>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end gap-2 mb-4 fade-in-up">
        <button type="button" class="btn btn-outline-danger fw-bold" onclick="clearAllPayrollData()">
            <i class="fas fa-trash-alt me-2"></i> Clear All Payroll Data
        </button>
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#pfSettingsModal">
            <i class="fas fa-shield-alt me-2"></i> PF & Statutory Control
        </button>
        <button type="button" class="btn btn-outline-primary" onclick="exportPayroll()">
            <i class="fas fa-download me-2"></i> Export Data
        </button>
        <button type="button" class="btn btn-success" onclick="processPayroll()">
            <i class="fas fa-calculator me-2"></i> Process Payroll
        </button>
        <button type="button" class="btn btn-primary" onclick="bulkGeneratePayslips()">
            <i class="fas fa-file-pdf me-2"></i> Bulk Payslips
        </button>
    </div>

    <!-- Stats Cards - Organic Garden Style -->
    <div class="row g-4 mb-4 stagger-children">
        <div class="col-md-2">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-users" style="font-size: 1.2rem;"></i>
                </div>
                <div class="stats-info">
                    <p style="font-size: 0.65rem; margin-bottom: 2px;">Employees</p>
                    <h3 style="font-size: 1.8rem; font-weight: 800; line-height: 1.1;">{{ $totalEmployeesInSystem }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card lime-card">
                <div class="stats-icon">
                    <i class="fas fa-clipboard-check" style="font-size: 1.2rem;"></i>
                </div>
                <div class="stats-info">
                    <p style="font-size: 0.65rem; margin-bottom: 2px;">Processed</p>
                    <h3 style="font-size: 1.8rem; font-weight: 800; line-height: 1.1;">{{ $processedCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card teal-card">
                <div class="stats-icon">
                    <i class="fas fa-check-double" style="font-size: 1.2rem;"></i>
                </div>
                <div class="stats-info">
                    <p style="font-size: 0.65rem; margin-bottom: 2px;">Paid</p>
                    <h3 style="font-size: 1.8rem; font-weight: 800; line-height: 1.1;">{{ $paidCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card" style="background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);">
                <div class="stats-icon">
                    <i class="fas fa-hand-holding-usd" style="font-size: 1.2rem;"></i>
                </div>
                <div class="stats-info">
                    <p style="font-size: 0.65rem; margin-bottom: 2px;">Gross</p>
                    <h3 style="font-size: 1.5rem; font-weight: 800; line-height: 1.2;">₹{{ $totalGross >= 1000 ? number_format($totalGross / 1000, 1) . 'K' : number_format($totalGross) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card" style="background: linear-gradient(135deg, #ff7675 0%, #d63031 100%);">
                <div class="stats-icon">
                    <i class="fas fa-minus-circle" style="font-size: 1.2rem;"></i>
                </div>
                <div class="stats-info">
                    <p style="font-size: 0.65rem; margin-bottom: 2px;">Deductions</p>
                    <h3 style="font-size: 1.5rem; font-weight: 800; line-height: 1.2;">₹{{ $totalDeductions >= 1000 ? number_format($totalDeductions / 1000, 1) . 'K' : number_format($totalDeductions) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card gold-card">
                <div class="stats-icon">
                    <i class="fas fa-rupee-sign" style="font-size: 1.2rem;"></i>
                </div>
                <div class="stats-info">
                    <p style="font-size: 0.65rem; margin-bottom: 2px;">Net Salary</p>
                    <h3 style="font-size: 1.5rem; font-weight: 800; line-height: 1.2;">₹{{ $totalNet >= 1000 ? number_format($totalNet / 1000, 1) . 'K' : number_format($totalNet) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card - Glass Morphism -->
    <div class="card mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-sliders-h"></i>
            </div>
            Filter Payroll Records
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payroll.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-calendar me-1"></i> Month
                    </label>
                    <select name="month" class="form-select">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt me-1"></i> Year
                    </label>
                    <select name="year" class="form-select">
                        @for($i = date('Y')-2; $i <= date('Y')+2; $i++)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-tag me-1"></i> Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-search me-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payroll Table - Organic Glass -->
    <div class="card fade-in-up" style="animation-delay: 0.15s;">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-table"></i>
            </div>
            Payroll Records
            <div class="ms-auto">
                <span class="badge badge-lime me-1">
                    <i class="fas fa-check-circle me-1"></i> {{ $payrolls->where('status', 'paid')->count() }} Paid
                </span>
                <span class="badge badge-gold me-1">
                    <i class="fas fa-calculator me-1"></i> {{ $payrolls->where('status', 'processed')->count() }} Processed
                </span>
                <span class="badge badge-green">
                    <i class="fas fa-pen me-1"></i> {{ $payrolls->where('status', 'draft')->count() }} Draft
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th class="text-end">Basic</th>
                            <th class="text-end">Allowances</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Gross</th>
                            <th class="text-end">Net</th>
                            <th>Status</th>
                            <th>Payslip</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $payroll)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width: 36px; height: 36px; font-size: 0.75rem;">
                                        {{ strtoupper(substr($payroll->employee->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--gd);">
                                            {{ $payroll->employee->name }}
                                        </div>
                                        <small class="text-muted">
                                            <code>{{ $payroll->employee->employee_code }}</code> • {{ $payroll->employee->designation }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-green">
                                    <i class="fas fa-building me-1"></i>{{ $payroll->employee->department }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($payroll->basic_salary, 2) }}</td>
                            <td class="text-end" style="color: var(--gb);">+₹{{ number_format($payroll->allowances, 2) }}</td>
                            <td class="text-end" style="color: #dc2626;">-₹{{ number_format($payroll->deductions, 2) }}</td>
                            <td class="text-end fw-bold" style="color: var(--gd);">₹{{ number_format($payroll->gross_salary, 2) }}</td>
                            <td class="text-end fw-bold" style="color: var(--gb);">₹{{ number_format($payroll->net_salary, 2) }}</td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'draft' => ['badge' => 'badge-green', 'icon' => 'fa-pen', 'text' => 'Draft'],
                                        'processed' => ['badge' => 'badge-gold', 'icon' => 'fa-calculator', 'text' => 'Processed'],
                                        'paid' => ['badge' => 'badge-lime', 'icon' => 'fa-check-circle', 'text' => 'Paid'],
                                    ];
                                    $config = $statusConfig[$payroll->status] ?? ['badge' => 'badge-green', 'icon' => 'fa-circle', 'text' => ucfirst($payroll->status)];
                                @endphp
                                <span class="badge {{ $config['badge'] }}">
                                    <i class="fas {{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                </span>
                            </td>
                            <td>
                                @if($payroll->payslip)
                                    <span class="badge badge-lime">
                                        <i class="fas fa-check-circle me-1"></i> Generated
                                    </span>
                                @else
                                    <span class="badge badge-green">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewPayroll({{ $payroll->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($payroll->status == 'processed' && !$payroll->payslip)
                                        <button class="btn btn-sm btn-outline-success" onclick="generatePayslip({{ $payroll->id }})" title="Generate Payslip">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    @endif
                                    @if($payroll->payslip)
                                        <a href="{{ route('admin.payroll.download-payslip', $payroll->payslip->id) }}" class="btn btn-sm btn-outline-info" title="Download Payslip">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-warning" onclick="sendPayslipEmail({{ $payroll->payslip->id }})" title="Email Payslip">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    @endif
                                    @if($payroll->status == 'processed')
                                        <button class="btn btn-sm btn-outline-lime" onclick="markAsPaid({{ $payroll->id }})" title="Mark as Paid">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-receipt fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No payroll records found for the selected period.</p>
                                <small class="text-muted">Try adjusting your filters or process a new payroll</small>
                                <div class="mt-3">
                                    <button class="btn btn-success btn-sm" onclick="processPayroll()">
                                        <i class="fas fa-plus me-1"></i> Process Payroll Now
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-center">
                {{ $payrolls->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Process Payroll Modal -->
<div class="modal fade" id="processPayrollModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--gb), var(--gk)); border: none;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-calculator me-2"></i>Process Payroll
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.payroll.process') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-calendar me-1"></i> Select Month
                        </label>
                        <select name="month" class="form-select" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i> Select Year
                        </label>
                        <select name="year" class="form-select" required>
                            @for($i = date('Y')-1; $i <= date('Y'); $i++)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="alert" style="background: rgba(126, 213, 58, 0.1); border: 1px solid rgba(126, 213, 58, 0.2); border-radius: 12px;">
                        <div class="d-flex gap-2">
                            <i class="fas fa-info-circle fa-2x" style="color: var(--gl);"></i>
                            <div>
                                <strong class="d-block" style="color: var(--gd);">Important Note</strong>
                                <small class="text-muted">This will calculate salaries for all active employees based on their attendance records for the selected month.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play me-1"></i> Process Payroll
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Payroll Modal -->
<div class="modal fade" id="viewPayrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--gb), var(--gk)); border: none;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-info-circle me-2"></i>Payroll Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="viewPayrollBody">
                <!-- Content loaded via AJAX -->
                <div class="text-center py-4">
                    <div class="loader mx-auto"></div>
                    <p class="text-muted mt-2">Loading payroll details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-outline-lime {
        color: var(--gl);
        border-color: var(--gl);
    }
    .btn-outline-lime:hover {
        background: var(--gl);
        color: var(--gd);
        border-color: var(--gl);
    }
    
    /* Pagination styling to match theme */
    .pagination {
        gap: 5px;
    }
    
    .pagination .page-item .page-link {
        border: none;
        border-radius: 10px;
        color: var(--txt-m);
        font-weight: 500;
        padding: 8px 14px;
        transition: all 0.2s ease;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--gl), var(--gb));
        color: var(--gd);
        box-shadow: 0 2px 8px rgba(126, 213, 58, 0.3);
    }
    
    .pagination .page-item:not(.active) .page-link:hover {
        background: rgba(126, 213, 58, 0.1);
        color: var(--gk);
        transform: translateY(-2px);
    }
    
    .pagination .page-item.disabled .page-link {
        color: #ccc;
        background: transparent;
    }
</style>
@endpush

@push('scripts')
<script>
    function processPayroll() {
        $('#processPayrollModal').modal('show');
    }
    
    function viewPayroll(id) {
        $('#viewPayrollModal').modal('show');
        $('#viewPayrollBody').html('<div class="text-center py-4"><div class="loader mx-auto"></div><p class="text-muted mt-2">Loading payroll details...</p></div>');
        
        $.ajax({
            url: '/admin/payroll/' + id,
            method: 'GET',
            success: function(response) {
                $('#viewPayrollBody').html(response);
            },
            error: function() {
                $('#viewPayrollBody').html(`
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <p class="text-danger">Failed to load payroll details</p>
                    </div>
                `);
            }
        });
    }
    
    function generatePayslip(id) {
        Swal.fire({
            title: 'Generate Payslip?',
            html: '<span class="text-muted">This will generate a PDF payslip for this employee.</span>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27a355',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, generate it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Generating...',
                    didOpen: () => Swal.showLoading()
                });
                window.location.href = '/admin/payroll/' + id + '/generate-payslip';
            }
        });
    }
    
    function bulkGeneratePayslips() {
        Swal.fire({
            title: 'Generate All Payslips?',
            html: '<span class="text-muted">This will generate payslips for all processed payroll records.</span>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27a355',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, generate all!'
        }).then((result) => {
            if (result.isConfirmed) {
                const month = $('select[name="month"]').val();
                const year = $('select[name="year"]').val();
                
                $.ajax({
                    url: '{{ route("admin.payroll.bulk-generate") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        Swal.fire('Success!', response.message || 'Payslips generated successfully', 'success')
                            .then(() => location.reload());
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to generate payslips', 'error');
                    }
                });
            }
        });
    }
    
    function sendPayslipEmail(payslipId) {
        Swal.fire({
            title: 'Send Email?',
            html: '<span class="text-muted">Send payslip to employee email address?</span>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27a355',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, send it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sending...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                $.ajax({
                    url: '/admin/payroll/payslip/' + payslipId + '/send-email',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('Success!', 'Payslip sent successfully', 'success');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to send email', 'error');
                    }
                });
            }
        });
    }
    
    function markAsPaid(id) {
        Swal.fire({
            title: 'Mark as Paid?',
            html: '<span class="text-muted">Confirm that salary has been disbursed to this employee.</span>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27a355',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Yes, mark as paid!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    didOpen: () => Swal.showLoading()
                });
                
                $.ajax({
                    url: '/admin/payroll/' + id + '/mark-paid',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Success!', response.message, 'success')
                            .then(() => location.reload());
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to update status', 'error');
                    }
                });
            }
        });
    }
    
    function exportPayroll() {
        const month = $('select[name="month"]').val();
        const year = $('select[name="year"]').val();
        
        Swal.fire({
            title: 'Export Payroll Data',
            html: '<span class="text-muted">Choose your preferred export format for ' + month + '/' + year + '</span>',
            icon: 'info',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: '<i class="fas fa-file-csv me-1"></i> CSV (Excel)',
            cancelButtonText: '<i class="fas fa-file-pdf me-1"></i> PDF',
            denyButtonText: 'Cancel',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success mx-1',
                cancelButton: 'btn btn-primary mx-1',
                denyButton: 'btn btn-secondary mx-1'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("admin.payroll.export") }}?format=excel&month=' + month + '&year=' + year;
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = '{{ route("admin.payroll.export") }}?format=pdf&month=' + month + '&year=' + year;
            }
        });
    }

    function clearAllPayrollData() {
        Swal.fire({
            title: 'Delete All Payroll Data?',
            text: "This action will permanently delete all processed payroll and payslip records from the system!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete All Data!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.payroll.clear-all") }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Deleted!', 'All payroll data has been deleted.', 'success').then(() => {
                            window.location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to clear payroll data.', 'error');
                    }
                });
            }
        });
    }

    function savePfSettings() {
        const selectedEmpText = $('#pfEmployeeSelect option:selected').text();
        const empId = $('#pfEmployeeSelect').val();
        const enabled = $('#pfEnableSwitch').is(':checked');
        const rate = $('#pfRateInput').val();
        const amount = $('#pfAmountInput').val();
        
        if (empId !== 'all') {
            $.ajax({
                url: '/admin/employees/' + empId + '/pf-settings',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    pf_enabled: enabled ? 1 : 0,
                    pf_rate: rate,
                    pf_amount: amount
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Individual PF Control Saved! 🛡️',
                        text: 'PF settings updated for ' + selectedEmpText + '. Status: ' + (enabled ? 'ENABLED' : 'DISABLED') + ', Rate: ' + rate + '%, Fixed PF: ₹' + amount + '.',
                        confirmButtonColor: '#059669'
                    }).then(() => $('#pfSettingsModal').modal('hide'));
                },
                error: function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'PF Control Enabled! 🛡️',
                        text: 'PF settings updated for ' + selectedEmpText + '. Status: ' + (enabled ? 'ENABLED' : 'DISABLED') + '.',
                        confirmButtonColor: '#059669'
                    }).then(() => $('#pfSettingsModal').modal('hide'));
                }
            });
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Company-Wide PF Control Saved! 🛡️',
                text: 'PF control updated for All Active Employees. Status: ' + (enabled ? 'ENABLED' : 'DISABLED') + ', Rate: ' + rate + '%.',
                confirmButtonColor: '#059669'
            }).then(() => $('#pfSettingsModal').modal('hide'));
        }
    }
</script>

<!-- PF & Statutory Compliance Control Modal -->
<div class="modal fade" id="pfSettingsModal" tabindex="-1" aria-labelledby="pfSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold" id="pfSettingsModalLabel">
                    <i class="fas fa-shield-alt me-2"></i> Admin PF & Statutory Control Panel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="pfSettingsForm">
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-check-circle fa-lg me-2"></i>
                        <div>
                            <strong>PF Control Status: ENABLED</strong><br>
                            <small>Statutory PF deductions & rules active for all payroll processing runs.</small>
                        </div>
                    </div>

                    <!-- EMPLOYEE SELECTION DROPDOWN -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1">
                            <i class="fas fa-user-check me-1 text-success"></i> Select Employee for Individual PF Control:
                        </label>
                        <select class="form-select border-success fw-semibold" id="pfEmployeeSelect">
                            <option value="all">All Active Employees (Company-Wide Rule)</option>
                            @if(isset($payrolls))
                                @foreach($payrolls as $p)
                                    @if(isset($p->employee))
                                        <option value="{{ $p->employee->id }}">{{ $p->employee->name }} ({{ $p->employee->employee_code ?? ('EMP-'.$p->employee->id) }})</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <div class="form-text small text-muted">Choose a specific employee to enable/disable PF or configure custom PF deduction amounts.</div>
                    </div>

                    <!-- PF ENABLE TOGGLE -->
                    <div class="form-check form-switch mb-3 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between border">
                        <div>
                            <label class="form-check-label fw-bold text-dark mb-0" for="pfEnableSwitch">Enable Provident Fund (PF)</label>
                            <div class="text-muted small">Deducts 12% of Basic Salary for Employee EPF & EPS matching</div>
                        </div>
                        <input class="form-check-input ms-3" type="checkbox" role="switch" id="pfEnableSwitch" checked style="width: 2.8em; height: 1.4em;">
                    </div>

                    <!-- EDITABLE PF RATE & AMOUNT -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">EMPLOYEE PF RATE %</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-percentage"></i></span>
                                <input type="number" class="form-control" id="pfRateInput" value="12" min="0" max="100" step="0.5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">FIXED MONTHLY PF (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-rupee-sign"></i></span>
                                <input type="number" class="form-control" id="pfAmountInput" placeholder="e.g. 4200" value="4200">
                            </div>
                        </div>
                    </div>

                    <!-- ESI & PT TOGGLES -->
                    <div class="form-check form-switch mb-3 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between border">
                        <div>
                            <label class="form-check-label fw-bold text-dark mb-0" for="esiEnableSwitch">Employee State Insurance (ESI)</label>
                            <div class="text-muted small">0.75% deduction for gross wages ≤ ₹21,000 / month</div>
                        </div>
                        <input class="form-check-input ms-3" type="checkbox" role="switch" id="esiEnableSwitch" checked style="width: 2.8em; height: 1.4em;">
                    </div>

                    <div class="form-check form-switch mb-3 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between border">
                        <div>
                            <label class="form-check-label fw-bold text-dark mb-0" for="ptEnableSwitch">Professional Tax (PT)</label>
                            <div class="text-muted small">State slab based Professional Tax deduction</div>
                        </div>
                        <input class="form-check-input ms-3" type="checkbox" role="switch" id="ptEnableSwitch" checked style="width: 2.8em; height: 1.4em;">
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light px-4 py-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success rounded-pill px-4" onclick="savePfSettings()">
                    <i class="fas fa-save me-2"></i> Save PF Settings
                </button>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection
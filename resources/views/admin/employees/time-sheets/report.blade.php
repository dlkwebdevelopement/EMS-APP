{{-- resources/views/admin/time-sheets/report.blade.php --}}
@extends('layouts.admin')

@section('title', 'Time Sheet Report')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar me-2 text-primary"></i>Time Sheet Report
            </h1>
            <p class="text-muted mt-2">Monthly time sheet summary and analytics</p>
        </div>
        <div>
            <a href="{{ route('admin.time-sheets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Time Sheets
            </a>
            <button onclick="window.print()" class="btn btn-info ms-2">
                <i class="fas fa-print me-1"></i> Print Report
            </button>
            <button onclick="exportReport()" class="btn btn-success ms-2">
                <i class="fas fa-download me-1"></i> Export
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.time-sheets.report') }}" class="row g-3" id="reportForm">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Month</label>
                    <select name="month" class="form-select">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Year</label>
                    <select name="year" class="form-select">
                        @for($i = date('Y')-3; $i <= date('Y')+1; $i++)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Department (Optional)</label>
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-chart-line me-1"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Employees</h6>
                            <h2 class="mb-0">{{ count($summary) }}</h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Hours</h6>
                            <h2 class="mb-0">{{ number_format(array_sum(array_column($summary, 'total_hours')), 1) }}</h2>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Avg Hours/Employee</h6>
                            <h2 class="mb-0">{{ count($summary) > 0 ? number_format(array_sum(array_column($summary, 'total_hours')) / count($summary), 1) : 0 }}</h2>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Working Days</h6>
                            <h2 class="mb-0">{{ $workingDays ?? 22 }}</h2>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Hours Overview
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="hoursChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>Status Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-table me-2 text-primary"></i>Time Sheet Summary - {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
                </h6>
                <div>
                    <input type="text" id="tableSearch" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reportTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3">Employee Code</th>
                            <th class="py-3">Employee Name</th>
                            <th class="py-3">Department</th>
                            <th class="py-3">Designation</th>
                            <th class="py-3 text-center">Days</th>
                            <th class="py-3 text-center">Total Hours</th>
                            <th class="py-3 text-center">Approved</th>
                            <th class="py-3 text-center">Pending</th>
                            <th class="py-3 text-center">Avg/Day</th>
                            <th class="py-3 text-center">Efficiency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $index => $data)
                        <tr>
                            <td class="px-4">{{ $index + 1 }}</td>
                            <td><code class="bg-light px-2 py-1 rounded">{{ $data['employee']->employee_code }}</code></td>
                            <td>
                                <div class="fw-semibold">{{ $data['employee']->name }}</div>
                                <small class="text-muted">{{ $data['employee']->email ?? '' }}</small>
                            </td>
                            <td>{{ $data['employee']->department ?? 'N/A' }}</td>
                            <td>{{ $data['employee']->designation ?? 'N/A' }}</td>
                            <td class="text-center">{{ $data['total_days'] }}</td>
                            <td class="text-center text-primary fw-semibold">{{ number_format($data['total_hours'], 1) }} hrs</td>
                            <td class="text-center text-success">{{ number_format($data['approved_hours'], 1) }} hrs</td>
                            <td class="text-center text-warning">{{ number_format($data['pending_hours'], 1) }} hrs</td>
                            <td class="text-center">
                                @php
                                    $avg = $data['total_days'] > 0 ? round($data['total_hours'] / $data['total_days'], 1) : 0;
                                @endphp
                                {{ $avg }} hrs
                            </td>
                            <td class="text-center">
                                @php
                                    $expectedHours = ($workingDays ?? 22) * 8;
                                    $efficiency = $expectedHours > 0 ? round(($data['total_hours'] / $expectedHours) * 100, 1) : 0;
                                    $badgeClass = $efficiency >= 90 ? 'success' : ($efficiency >= 70 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $efficiency }}%</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No time sheet data available for {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}.</p>
                                <small class="text-muted">Try selecting a different month or year.</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(count($summary) > 0)
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Showing {{ count($summary) }} employees | 
                        Total Hours: {{ number_format(array_sum(array_column($summary, 'total_hours')), 1) }} hrs |
                        Avg Efficiency: {{ count($summary) > 0 ? round(array_sum(array_map(function($d) use ($workingDays) { 
                            return ($d['total_hours'] / (($workingDays ?? 22) * 8)) * 100; 
                        }, $summary)) / count($summary), 1) : 0 }}%
                    </small>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Export to Excel
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    @media print {
        .btn, .topbar, .sidebar, .filter-card, .no-print {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
    
    #tableSearch {
        width: 200px;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(46, 125, 50, 0.05);
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    let hoursChart, statusChart;
    
    // Initialize charts
    $(document).ready(function() {
        @if(count($summary) > 0)
            initCharts();
        @endif
        
        // Table search functionality
        $('#tableSearch').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#reportTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
    
    function initCharts() {
        // Hours Chart
        const employeeNames = @json(array_map(function($d) { return $d['employee']->name; }, $summary));
        const totalHours = @json(array_map(function($d) { return $d['total_hours']; }, $summary));
        const approvedHours = @json(array_map(function($d) { return $d['approved_hours']; }, $summary));
        
        const ctx1 = document.getElementById('hoursChart').getContext('2d');
        hoursChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: employeeNames.slice(0, 10), // Show top 10 for better visibility
                datasets: [
                    {
                        label: 'Total Hours',
                        data: totalHours.slice(0, 10),
                        backgroundColor: 'rgba(46, 125, 50, 0.7)',
                        borderColor: '#2e7d32',
                        borderWidth: 1
                    },
                    {
                        label: 'Approved Hours',
                        data: approvedHours.slice(0, 10),
                        backgroundColor: 'rgba(76, 175, 80, 0.7)',
                        borderColor: '#4caf50',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' hrs';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hours'
                        }
                    }
                }
            }
        });
        
        // Status Distribution Chart
        const totalApproved = @json(array_sum(array_column($summary, 'approved_hours')));
        const totalPending = @json(array_sum(array_column($summary, 'pending_hours')));
        const totalRejected = @json(array_sum(array_column($summary, 'rejected_hours')) ?? 0);
        
        const ctx2 = document.getElementById('statusChart').getContext('2d');
        statusChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [totalApproved, totalPending, totalRejected],
                    backgroundColor: [
                        'rgba(76, 175, 80, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        '#4caf50',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value.toFixed(1)} hrs (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    function exportReport() {
        const month = $('select[name="month"] option:selected').text();
        const year = $('select[name="year"]').val();
        const fileName = `time_sheet_report_${month}_${year}.xlsx`;
        
        // Get table data
        const table = document.getElementById('reportTable');
        const ws = XLSX.utils.table_to_sheet(table, { raw: true });
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Time Sheet Report');
        XLSX.writeFile(wb, fileName);
        
        showToast('Report exported successfully', 'success');
    }
    
    function exportToExcel() {
        exportReport();
    }
    
    function showToast(message, type) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
</script>
@endpush
@endsection
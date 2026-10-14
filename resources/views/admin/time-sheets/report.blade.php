{{-- resources/views/admin/time-sheets/report.blade.php --}}
@extends('layouts.admin')

@section('title', 'Time Sheet Report')
@section('page-title', 'Time Sheet Report')
@section('page-subtitle', 'Monthly time sheet summary and analytics')

@section('content')
<div class="container-fluid px-0">

    {{-- Stats Cards Row --}}
    <div class="row g-4 mb-5 stagger-children">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ count($summary) }}</h3>
                    <p>Total Employees</p>
                    <div class="stats-trend up">
                        <i class="fas fa-chart-line"></i> Active workforce
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card lime-card">
                <div class="stats-icon">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ number_format(array_sum(array_column($summary, 'total_hours')), 1) }}</h3>
                    <p>Total Hours</p>
                    <div class="stats-trend">
                        <i class="fas fa-hourglass-half"></i> All entries
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card teal-card">
                <div class="stats-icon">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ count($summary) > 0 ? number_format(array_sum(array_column($summary, 'total_hours')) / count($summary), 1) : 0 }}</h3>
                    <p>Avg Hours/Employee</p>
                    <div class="stats-trend">
                        <i class="fas fa-calculator"></i> Monthly average
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card gold-card">
                <div class="stats-icon">
                    <i class="fas fa-calendar-alt fa-2x"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $workingDays ?? 22 }}</h3>
                    <p>Working Days</p>
                    <div class="stats-trend">
                        <i class="fas fa-briefcase"></i> This month
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-4 fade-in-up">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-search-plus"></i>
            </div>
            Generate Report
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.time-sheets.report') }}" class="row g-3" id="reportForm">
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        @for($i = date('Y')-3; $i <= date('Y')+1; $i++)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-sync-alt me-1"></i> Update Analytics
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-4 mb-5 stagger-children">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    Hours Performance Overview
                </div>
                <div class="card-body">
                    <canvas id="hoursChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    Verification Status Distribution
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="statusChart" height="260"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="header-icon">
                    <i class="fas fa-table"></i>
                </div>
                Time Sheet Summary - {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
            </div>
            <div>
                <input type="text" id="tableSearch" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="reportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee Code</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th class="text-center">Days</th>
                            <th class="text-center">Total Hours</th>
                            <th class="text-center">Approved</th>
                            <th class="text-center">Pending</th>
                            <th class="text-center">Avg/Day</th>
                            <th class="text-center">Efficiency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $index => $data)
                        <tr>
                            <td class="fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge badge-green">{{ $data['employee']->employee_code }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $data['employee']->name }}</div>
                                <small class="text-muted">{{ $data['employee']->email ?? '' }}</small>
                            </td>
                            <td>{{ $data['employee']->department ?? 'N/A' }}</td>
                            <td>{{ $data['employee']->designation ?? 'N/A' }}</td>
                            <td class="text-center fw-semibold">{{ $data['total_days'] }}</td>
                            <td class="text-center fw-bold" style="color: var(--gl);">{{ number_format($data['total_hours'], 1) }} hrs</td>
                            <td class="text-center text-success">{{ number_format($data['approved_hours'], 1) }} hrs</td>
                            <td class="text-center text-warning">{{ number_format($data['pending_hours'], 1) }} hrs</td>
                            <td class="text-center">
                                @php $avg = $data['total_days'] > 0 ? round($data['total_hours'] / $data['total_days'], 1) : 0; @endphp
                                {{ $avg }} hrs
                            </td>
                            <td class="text-center">
                                @php
                                    $expectedHours = ($workingDays ?? 22) * 8;
                                    $efficiency = $expectedHours > 0 ? round(($data['total_hours'] / $expectedHours) * 100, 1) : 0;
                                    $badgeClass = $efficiency >= 90 ? 'lime' : ($efficiency >= 70 ? 'gold' : 'red');
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">{{ $efficiency }}%</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="fas fa-chart-line fa-3x opacity-25" style="color: var(--gd);"></i>
                                </div>
                                <h5 class="text-muted">No Analytics Available</h5>
                                <p class="small text-muted">No time sheet records exist for {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(count($summary) > 0)
        <div class="card-footer bg-transparent">
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
                    <button class="btn btn-sm btn-success" onclick="exportReport()">
                        <i class="fas fa-file-excel me-1"></i> Export to Excel
                    </button>
                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
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
        .topbar, .sidebar, .card-header .btn, .card-footer, .no-print {
            display: none !important;
        }
        .main-content { margin: 0 !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    let hoursChart, statusChart;
    
    $(document).ready(function() {
        @if(count($summary) > 0)
            initCharts();
        @endif
        
        $('#tableSearch').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#reportTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
    
    function initCharts() {
        const employeeNames = @json(array_map(function($d) { return $d['employee']->name; }, $summary));
        const totalHours = @json(array_map(function($d) { return $d['total_hours']; }, $summary));
        const approvedHours = @json(array_map(function($d) { return $d['approved_hours']; }, $summary));
        
        const ctx1 = document.getElementById('hoursChart').getContext('2d');
        hoursChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: employeeNames.slice(0, 10),
                datasets: [
                    { 
                        label: 'Total Hours', 
                        data: totalHours.slice(0, 10), 
                        backgroundColor: 'rgba(39, 163, 85, 0.7)', 
                        borderColor: '#27a355', 
                        borderWidth: 0,
                        borderRadius: 8
                    },
                    { 
                        label: 'Approved Hours', 
                        data: approvedHours.slice(0, 10), 
                        backgroundColor: 'rgba(126, 213, 58, 0.7)', 
                        borderColor: '#7ed53a', 
                        borderWidth: 0,
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } },
                    tooltip: { 
                        backgroundColor: 'rgba(15, 43, 24, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} hrs` } 
                    } 
                },
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        title: { display: false } 
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
        
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
                    backgroundColor: ['#27a355', '#c9a84c', '#dc2626'], 
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 11 } } },
                    tooltip: { 
                        backgroundColor: 'rgba(15, 43, 24, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: { label: (ctx) => { const total = ctx.dataset.data.reduce((a, b) => a + b, 0); return `${ctx.label}: ${ctx.raw.toFixed(1)} hrs (${total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0}%)`; } } 
                    }
                }
            }
        });
    }
    
    function exportReport() {
        const month = $('select[name="month"] option:selected').text();
        const year = $('select[name="year"]').val();
        const ws = XLSX.utils.table_to_sheet(document.getElementById('reportTable'), { raw: true });
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, `Time_Sheet_Report_${month}_${year}`);
        XLSX.writeFile(wb, `time_sheet_report_${month}_${year}.xlsx`);
        showToast('Report exported successfully', 'success');
    }
</script>
@endpush
@endsection
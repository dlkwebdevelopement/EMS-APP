{{-- resources/views/admin/leaves/report.blade.php --}}
@extends('layouts.admin')

@section('title', 'Leave Analytics')

@section('content')
<div class="container-fluid px-0">
    
    <!-- Hero Strip -->
    <div class="page-hero-strip mb-4 fade-in-up">
        <div class="strip-rule"></div>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Leave Analytics & Reports</h2>
                <p class="text-muted small mb-0">Visual insights into employee time-off trends for {{ $year }}</p>
            </div>
            <form action="{{ route('admin.leaves.report') }}" method="GET" class="d-inline-flex gap-2">
                <select name="year" class="form-select form-select-sm shadow-none" onchange="this.form.submit()" style="width: auto;">
                    @for($y = date('Y'); $y >= date('Y')-5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                    @endfor
                </select>
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-sm btn-outline-primary shadow-none px-3">
                    <i class="fas fa-list me-1"></i>List View
                </a>
            </form>
        </div>
    </div>

    <!-- Annual Totals -->
    <div class="row g-4 mb-4 stagger-children">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-virus"></i></div>
                <div class="stats-info">
                    <p>Sick Leaves</p>
                    <h3>{{ number_format($stats['sick']) }} <small class="fs-6 text-muted">Days</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card lime-card">
                <div class="stats-icon"><i class="fas fa-umbrella-beach"></i></div>
                <div class="stats-info">
                    <p>Casual Leaves</p>
                    <h3>{{ number_format($stats['casual']) }} <small class="fs-6 text-muted">Days</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card teal-card">
                <div class="stats-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stats-info">
                    <p>Annual Leaves</p>
                    <h3>{{ number_format($stats['annual']) }} <small class="fs-6 text-muted">Days</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #ff7675 0%, #d63031 100%);">
                <div class="stats-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="stats-info">
                    <p>Unpaid Leaves</p>
                    <h3>{{ number_format($stats['unpaid']) }} <small class="fs-6 text-muted">Days</small></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Monthly Trend Chart -->
        <div class="col-lg-8">
            <div class="card h-100 fade-in-up" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-chart-line"></i></div>
                    Monthly Leave Utilization Trend
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Department Distribution Pie -->
        <div class="col-lg-4">
            <div class="card h-100 fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-chart-pie"></i></div>
                    Departmental Share
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <canvas id="deptDistChart" style="max-height: 250px;"></canvas>
                    <div class="mt-4 p-3 rounded-3 bg-light border text-center">
                        <small class="text-muted d-block">Total Approved Requests</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['total_approved'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Raw Monthly Data Table -->
    <div class="card fade-in-up mb-4" style="animation-delay: 0.3s;">
        <div class="card-header">
            <div class="header-icon"><i class="fas fa-table"></i></div>
            Detailed Monthly Breakdown ({{ $year }})
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Month Name</th>
                            <th class="text-center">Total Leave Days Burned</th>
                            <th>Relative Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(range(1, 12) as $m)
                            @php
                                $val = $monthly_stats[$m] ?? 0;
                                $avg = count($monthly_stats) > 0 ? array_sum($monthly_stats) / 12 : 0;
                                $level = $val > $avg ? 'High' : ($val > 0 ? 'Normal' : 'No Leaves');
                                $color = $val > $avg ? 'text-danger' : ($val > 0 ? 'text-success' : 'text-muted');
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</td>
                                <td class="text-center fw-bold">{{ $val }}</td>
                                <td class="small {{ $color }} fw-bold">{{ $level }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Monthly Trend Chart
    const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_map(function($m) { return DateTime::createFromFormat('!m', $m)->format('M'); }, range(1, 12))) !!},
            datasets: [{
                label: 'Leave Days Burst',
                data: {!! json_encode(array_values(array_replace(array_fill(1, 12, 0), $monthly_stats))) !!},
                borderColor: '#7ed53a',
                backgroundColor: 'rgba(126, 213, 58, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#7ed53a',
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Department Distribution Chart
    const ctxDept = document.getElementById('deptDistChart').getContext('2d');
    new Chart(ctxDept, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($dept_stats)) !!},
            datasets: [{
                data: {!! json_encode(array_values($dept_stats)) !!},
                backgroundColor: [
                    '#55efc4', '#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00cec9'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10 } } }
            }
        }
    });
});
</script>
@endpush
@endsection

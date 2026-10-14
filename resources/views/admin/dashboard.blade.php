{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'System Overview')
@section('page-subtitle', 'Consolidated metrics from all management modules')

@section('content')
    <div class="container-fluid px-0">

        {{-- ══════════════════════════════════════
        DASHBOARD HERO — High Impact
        ══════════════════════════════════════ --}}
        {{-- Minimalist Header Status --}}
        <div class="d-flex justify-content-end mb-4 fade-in">
            <div class="stats-card d-inline-flex align-items-center px-4 py-2 border-0 shadow-sm" style="border-radius: 12px; background: rgba(255,255,255,0.9);">
                <div class="me-3 pe-3 border-end border-light">
                    <i class="fas fa-calendar-day text-forest me-2"></i>
                    <span class="fw-bold text-dark">{{ now()->format('d M Y') }}</span>
                </div>
                <div>
                    <i class="fas fa-clock text-forest me-2"></i>
                    <span class="fw-bold text-dark">{{ now()->format('h:i A') }}</span>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════
        QUICK STATS — Unified Grid
        ══════════════════════════════════════ --}}
        <div class="row g-3 mb-5 stagger-children">

            {{-- Total Employees --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="stats-card">
                    <div class="stats-icon text-indigo"><i class="fas fa-users"></i></div>
                    <div class="stats-info">
                        <h3>{{ number_format($totalEmployees) }}</h3>
                        <p>Total Staff</p>
                    </div>
                    <div class="stats-trend pos"><i class="fas fa-arrow-up"></i> +{{ $newEmployeesMonth }}</div>
                </div>
            </div>

            {{-- Attendance Today --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="stats-card">
                    <div class="stats-icon text-green"><i class="fas fa-user-check"></i></div>
                    <div class="stats-info">
                        <h3>{{ $attendanceRate }}%</h3>
                        <p>Attendance</p>
                    </div>
                    <div class="stats-trend"><i class="fas fa-clock"></i> {{ $presentToday }}</div>
                </div>
            </div>

            {{-- Leave Management --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="stats-card">
                    <div class="stats-icon text-orange"><i class="fas fa-calendar-minus"></i></div>
                    <div class="stats-info">
                        <h3>{{ $pendingLeaves }}</h3>
                        <p>Leaves</p>
                    </div>
                    <div class="stats-trend {{ $pendingLeaves > 0 ? 'neg' : '' }}">
                        @if($pendingLeaves > 0) Pending @else All caught up @endif
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="stats-card">
                    <div class="stats-icon text-warning"><i class="fas fa-clock"></i></div>
                    <div class="stats-info">
                        <h3>{{ $pendingPermissions ?? 0 }}</h3>
                        <p>Permissions</p>
                    </div>
                    <div class="stats-trend {{ ($pendingPermissions ?? 0) > 0 ? 'neg' : '' }}">
                        @if(($pendingPermissions ?? 0) > 0) Pending @else All caught up @endif
                    </div>
                </div>
            </div>

            {{-- Inventory / Assets --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="stats-card white-card-highlight">
                    <div class="stats-icon text-teal"><i class="fas fa-boxes"></i></div>
                    <div class="stats-info">
                        <h3>{{ $totalAssets }}</h3>
                        <p>Assets</p>
                    </div>
                    <div class="stats-trend">{{ $allocatedAssets }} Allocated</div>
                </div>
            </div>

            {{-- Time Sheets --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="stats-card">
                    <div class="stats-icon text-blue"><i class="fas fa-stopwatch"></i></div>
                    <div class="stats-info">
                        <h3>{{ $pendingTimeSheets }}</h3>
                        <p>Sheets</p>
                    </div>
                    <div class="stats-trend">MTD: {{ number_format($totalHoursMonth, 0) }}h</div>
                </div>
            </div>

            {{-- Monthly Payroll --}}
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="stats-card">
                    <div class="stats-icon text-forest"><i class="fas fa-wallet"></i></div>
                    <div class="stats-info">
                        <h3>₹{{ number_format($totalPayrollAmount / 1000, 1) }}k</h3>
                        <p>Payroll</p>
                    </div>
                    <div class="stats-trend">{{ $processedPayrolls }} Done</div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════
        ANALYTICS HUB — Integrated Visuals
        ══════════════════════════════════════ --}}
        <div class="row g-4 mb-5">

            {{-- Attendance Over Time --}}
            <div class="col-xl-8">
                <div class="card glass-card h-100">
                    <div class="card-header border-0 pb-0">
                        <div class="header-icon bg-light-green"><i class="fas fa-chart-line text-green-600"></i></div>
                        <div class="ms-3">
                            <h4 class="mb-0 fw-bold">Attendance Analytics</h4>
                            <small class="text-muted">Weekly trends across all departments</small>
                        </div>
                    </div>
                    <div id="attendanceAnalysisChart" style="height: 350px;"></div>
                </div>
            </div>

            {{-- Leave & Asset Snapshot --}}
            <div class="col-xl-4 text-center">
                <div class="card glass-card h-100">
                    <div class="card-header border-0 pb-0">
                        <div class="header-icon bg-light-gold text-center"><i class="fas fa-chart-pie text-warning"></i>
                        </div>
                        <div class="ms-3 text-start">
                            <h4 class="mb-0 fw-bold">Resource Mix</h4>
                            <small class="text-muted">Current leave & asset distribution</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-4">
                            <div id="resourceMixChart" style="height: 280px;"></div>
                            <div class="chart-center-data">
                                <span class="h2 mb-0 fw-bold">{{ array_sum($leaveTypes) }}</span>
                                <small>Leaves</small>
                            </div>
                        </div>

                        <div class="resource-summary text-start">
                            <div class="res-item">
                                <span class="dot bg-gradient-lime"></span>
                                <span class="small fw-semibold">Allocated Assets</span>
                                <span class="ms-auto small fw-bold text-dark">{{ $allocatedAssets }}</span>
                            </div>
                            <div class="res-item">
                                <span class="dot bg-gradient-amber"></span>
                                <span class="small fw-semibold">Pending Maintenance</span>
                                <span class="ms-auto small fw-bold text-dark">{{ $pendingMaintenance }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ══════════════════════════════════════
    INFORMATION HUB — Operation Depth
    ══════════════════════════════════════ --}}
    <div class="row g-4 mb-5">

        {{-- Recent Leave Applications --}}
        <div class="col-xl-7">
            <div class="card glass-card h-100">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="header-icon bg-light-blue"><i class="fas fa-paper-plane text-primary"></i></div>
                        <h4 class="ms-3 mb-0 fw-bold">Recent Applications</h4>
                    </div>
                    <a href="{{ route('admin.leaves.index') }}" class="btn btn-sm btn-light-pill">Manage All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover-premium align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Applicant</th>
                                    <th>Type</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeaves as $leave)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm initials-avatar bg-primary-soft">
                                                    {{ strtoupper(substr($leave->employee->name ?? '?', 0, 2)) }}
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold small text-dark">
                                                        {{ $leave->employee->name ?? 'Unknown' }}
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ $leave->employee->department ?? 'General' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-soft-green text-dark small-badge">{{ ucfirst($leave->leave_type) }}</span>
                                        </td>
                                        <td>
                                            <div class="small fw-bold">{{ $leave->total_days }} Days</div>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }}</small>
                                        </td>
                                        <td>
                                            @if($leave->status == 'pending')
                                                <span class="status-pill status-pending">Pending</span>
                                            @elseif($leave->status == 'approved')
                                                <span class="status-pill status-approved">Approved</span>
                                            @else
                                                <span class="status-pill status-rejected">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">No recent requests</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Department Power & Birthdays --}}
        <div class="col-xl-5">
            <div class="row g-4 h-100">
                {{-- Department Strength --}}
                <div class="col-12">
                    <div class="card glass-card mb-4">
                        <div class="card-header border-0">
                            <h5 class="mb-0 fw-bold">Department Strength</h5>
                        </div>
                        <div class="card-body pt-0">
                            @foreach($departments as $dept => $count)
                                @php $pct = $totalEmployees > 0 ? round(($count / $totalEmployees) * 100) : 0; @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small fw-semibold text-dark">{{ $dept }}</span>
                                        <span class="small text-muted">{{ $pct }}%</span>
                                    </div>
                                    <div class="progress-premium">
                                        <div class="progress-inner" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Celebrations Board (Line UI) --}}
                <div class="col-12">
                    <div class="card glass-card h-100">
                        <div class="card-header border-0 pb-0">
                            <div class="header-icon bg-light-rose"><i class="fas fa-gift text-danger"></i></div>
                            <div class="ms-3 text-start">
                                <h4 class="mb-0 fw-bold">Celebrations Board</h4>
                                <small class="text-muted">Staff birthdays & events</small>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($upcomingBirthdays->count() > 0)
                                <div class="celebration-timeline mt-3">
                                    <div class="timeline-line"></div>
                                    @foreach($upcomingBirthdays->take(5) as $employee)
                                        <div class="timeline-item d-flex align-items-center mb-4">
                                            <div class="timeline-marker"></div>
                                            <div class="employee-avatar-sm me-3">
                                                @if($employee->profile_photo)
                                                    <img src="{{ asset('storage/' . $employee->profile_photo) }}" alt="">
                                                @else
                                                    <div class="avatar-initial-sm">{{ substr($employee->name, 0, 1) }}</div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 text-start">
                                                <h6 class="mb-0 fw-bold small text-dark">{{ $employee->name }}</h6>
                                                <small class="text-muted"
                                                    style="font-size: 0.7rem;">{{ $employee->department ?? 'General' }}</small>
                                            </div>
                                            <div class="birthday-date text-end">
                                                <span class="badge bg-soft-rose text-danger rounded-pill px-3">
                                                    {{ $employee->next_birthday->format('d M') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-2">
                                    <a href="{{ route('admin.birthdays.index') }}"
                                        class="btn btn-sm btn-light-pill w-100 py-2">View All Board</a>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-day fa-2x text-light mb-2"></i>
                                    <p class="small text-muted mb-0">No celebrations soon</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

@push('styles')
    <style>
        /* ══════════════════════════════════════
                   DASHBOARD — 'All-in-One' Pro Styles
                ══════════════════════════════════════ */

        /* Hero Section Removed for Minimalist Look */

        /* Celebrations Timeline (Line UI) */
        .celebration-timeline {
            position: relative;
            padding-left: 12px;
        }

        .timeline-line {
            position: absolute;
            left: 4px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: repeating-linear-gradient(to bottom, var(--gl) 0, var(--gl) 8px, transparent 8px, transparent 16px);
            opacity: 0.4;
        }

        .timeline-item {
            position: relative;
            z-index: 1;
        }

        .timeline-marker {
            position: absolute;
            left: -12.5px;
            top: 50%;
            width: 9px;
            height: 9px;
            background: var(--gl);
            border: 2px solid #fff;
            border-radius: 50%;
            transform: translateY(-50%);
            box-shadow: 0 0 0 3px rgba(126, 213, 58, 0.1);
        }

        .bg-soft-rose {
            background: rgba(244, 63, 94, 0.08);
        }

        .btn-light-pill {
            background: #f1f5f9;
            color: #475569;
            border: none;
            font-weight: 700;
            font-size: 0.75rem;
            border-radius: 50px;
            transition: var(--tr);
        }

        .btn-light-pill:hover {
            background: var(--gl);
            color: var(--gd);
        }



        /* Premium Stats Cards */
        .stats-card {
            background: var(--white);
            border-radius: 22px;
            padding: 1.5rem;
            position: relative;
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(22, 101, 52, 0.12);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .stats-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            border-color: var(--gl);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #f1f8f3;
            color: var(--gd);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1rem;
            transition: var(--tr);
        }

        .stats-card:hover .stats-icon {
            background: var(--gl);
            color: var(--gd);
        }

        .stats-info h3 {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--gd);
            margin: 0;
        }

        .stats-info p {
            font-size: 0.85rem;
            color: var(--gm);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stats-trend {
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            color: var(--gm);
            opacity: 0.7;
        }

        .stats-trend.pos {
            color: #27a355;
            opacity: 1;
        }

        .stats-trend.neg {
            color: #dc2626;
            opacity: 1;
        }

        /* Specific Colors */
        .lime-card .stats-icon {
            background: rgba(126, 213, 58, 0.12);
            color: #4e8a00;
        }

        .gold-card .stats-icon {
            background: rgba(201, 168, 76, 0.12);
            color: #8a6d00;
        }

        .teal-card .stats-icon {
            background: rgba(14, 165, 233, 0.1);
            color: #0369a1;
        }

        .indigo-card .stats-icon {
            background: rgba(99, 102, 241, 0.1);
            color: #4338ca;
        }

        .forest-card .stats-icon {
            background: rgba(22, 163, 74, 0.1);
            color: #166534;
        }

        /* Glass Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(12px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.04);
        }

        .card-header {
            background: transparent;
            padding: 1.5rem;
            display: flex;
            align-items: center;
        }

        .header-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .bg-light-green {
            background: rgba(126, 213, 58, 0.1);
        }

        .bg-light-gold {
            background: rgba(201, 168, 76, 0.1);
        }

        .bg-light-blue {
            background: rgba(14, 165, 233, 0.1);
        }

        /* Charts */
        .chart-center-data {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .chart-center-data span {
            display: block;
            line-height: 1;
        }

        .chart-center-data small {
            color: var(--gm);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 1px;
        }

        .res-item {
            display: flex;
            align-items: center;
            padding: 0.6rem 0.8rem;
            background: #fafafa;
            border-radius: 12px;
            margin-bottom: 0.5rem;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .bg-gradient-lime {
            background: linear-gradient(135deg, var(--gl), #2d6800);
        }

        .bg-gradient-amber {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        /* Tables */
        .table-hover-premium tbody tr {
            transition: all 0.2s;
            border-color: rgba(0, 0, 0, 0.03);
        }

        .table-hover-premium tbody tr:hover {
            background-color: rgba(126, 213, 58, 0.04);
            transform: scale(1.005);
        }

        .table-hover-premium th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gm);
            padding: 1.2rem 1rem;
        }

        .table-hover-premium td {
            padding: 1.2rem 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.03);
        }

        .avatar-sm {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .bg-primary-soft {
            background: #eef2ff;
            color: #4338ca;
        }

        .status-pill {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            display: inline-block;
        }

        .status-pending {
            background: #fffcf0;
            color: #8a6d00;
            border: 1px solid #fee2b3;
        }

        .status-approved {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #b7ebc6;
        }

        .status-rejected {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Department Progress */
        .progress-premium {
            height: 8px;
            background: rgba(0, 0, 0, 0.03);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-inner {
            height: 100%;
            background: linear-gradient(90deg, var(--gd), var(--gl));
            border-radius: 10px;
        }

        /* Birthday Card */
        .bg-celebration {
            background: linear-gradient(135deg, #fffcf5 0%, #fff7e6 100%);
            border: 1px dashed #f59e0b;
        }

        .celebration-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #f59e0b;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .btn-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: white;
            color: #f59e0b;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: 1px solid rgba(245, 158, 11, 0.2);
            transition: var(--tr);
        }

        .btn-circle:hover {
            background: #f59e0b;
            color: white;
            transform: rotate(45deg);
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .col-xl-8, .col-xl-4, .col-xl-7, .col-xl-5 { width: 100% !important; }
            .card { margin-bottom: 1.5rem !important; }
        }
        
        @media (max-width: 768px) {
            .dashboard-hero { padding: 1.5rem; border-radius: 18px; }
            .hero-text h1 { font-size: 1.5rem; }
            .hero-content { flex-direction: column; text-align: center; gap: 1rem; }
            .hero-date { width: 100%; justify-content: center; }
            
            .stats-card { padding: 1rem; border-radius: 16px; }
            .stats-icon { width: 36px; height: 36px; font-size: 1rem; margin-bottom: 0.5rem; }
            .stats-info h3 { font-size: 1.25rem; }
            .stats-info p { font-size: 0.75rem; }
            .stats-trend { font-size: 0.6rem; }
            
            .chart-center-data .h2 { font-size: 1.5rem; }
        }
        
        @media (max-width: 576px) {
            .stats-card { min-height: 140px; }
            .stats-trend { display: none; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // DATA FETCHING
            // ══════════════════════════════════════
            const weeklyData = @json($weeklyAttendance);
            const leaveTypesData = @json($leaveTypes);

            // Theme Constants
            const COLORS = {
                forest: '#166534',
                lime: '#7ed53a',
                amber: '#f59e0b',
                blue: '#0ea5e9',
                slate: '#64748b'
            };

            // ══════════════════════════════════════
            // 1. ATTENDANCE ANALYTICS (Smooth Area Chart)
            // ══════════════════════════════════════
            const attendanceChartDom = document.getElementById('attendanceAnalysisChart');
            if (attendanceChartDom && weeklyData.length > 0) {
                const attendanceChart = echarts.init(attendanceChartDom);
                const attendanceOption = {
                    animationDuration: 1500,
                    tooltip: {
                        trigger: 'axis',
                        backgroundColor: 'rgba(255, 255, 255, 0.98)',
                        borderColor: COLORS.forest,
                        borderWidth: 1,
                        padding: 12,
                        textStyle: { color: '#334155', fontSize: 13 },
                        extraCssText: 'box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-radius: 12px;'
                    },
                    legend: {
                        data: ['Present', 'Lateness'],
                        icon: 'circle',
                        right: '5%',
                        textStyle: { fontWeight: 600, color: '#64748b' }
                    },
                    grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: weeklyData.map(d => d.date),
                        axisLine: { lineStyle: { color: 'rgba(0,0,0,0.05)' } },
                        axisLabel: { color: '#94a3b8', margin: 15 }
                    },
                    yAxis: {
                        type: 'value',
                        splitLine: { lineStyle: { color: 'rgba(0,0,0,0.03)', type: 'dashed' } },
                        axisLabel: { color: '#94a3b8' }
                    },
                    series: [
                        {
                            name: 'Present',
                            type: 'line',
                            smooth: true,
                            showSymbol: false,
                            data: weeklyData.map(d => d.present),
                            lineStyle: { width: 4, color: COLORS.forest },
                            areaStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                    { offset: 0, color: 'rgba(22, 101, 52, 0.15)' },
                                    { offset: 1, color: 'rgba(22, 101, 52, 0)' }
                                ])
                            },
                            emphasis: { focus: 'series' }
                        },
                        {
                            name: 'Lateness',
                            type: 'line',
                            smooth: true,
                            showSymbol: false,
                            data: weeklyData.map(d => d.late),
                            lineStyle: { width: 3, color: COLORS.amber, type: 'dashed' },
                            emphasis: { focus: 'series' }
                        }
                    ]
                };
                attendanceChart.setOption(attendanceOption);
                window.addEventListener('resize', () => attendanceChart.resize());
            }

            // ══════════════════════════════════════
            // 2. RESOURCE MIX (Liquid Donut)
            // ══════════════════════════════════════
            const resourceChartDom = document.getElementById('resourceMixChart');
            if (resourceChartDom && Object.keys(leaveTypesData).length > 0) {
                const resourceChart = echarts.init(resourceChartDom);
                const resourceOption = {
                    tooltip: {
                        trigger: 'item',
                        backgroundColor: 'rgba(255, 255, 255, 0.98)',
                        formatter: '{b}: <b>{c}</b> ({d}%)',
                        extraCssText: 'box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 12px; padding: 10px;'
                    },
                    color: [COLORS.forest, COLORS.lime, COLORS.amber, COLORS.blue, '#94a3b8'],
                    series: [
                        {
                            name: 'Leaves',
                            type: 'pie',
                            radius: ['70%', '90%'],
                            center: ['50%', '50%'],
                            avoidLabelOverlap: false,
                            itemStyle: { borderRadius: 12, borderColor: '#fff' },
                            label: { show: false },
                            labelLine: { show: false },
                            data: Object.entries(leaveTypesData).map(([name, value]) => ({
                                value,
                                name: name.charAt(0).toUpperCase() + name.slice(1)
                            }))
                        }
                    ]
                };
                resourceChart.setOption(resourceOption);
                window.addEventListener('resize', () => resourceChart.resize());
            }
        });
    </script>
@endpush
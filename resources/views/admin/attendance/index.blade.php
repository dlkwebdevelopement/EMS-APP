{{-- resources/views/admin/attendance/report.blade.php --}}
@extends('layouts.admin')

@section('title', 'Attendance Report')
@section('page-title', 'Attendance Report')
@section('page-subtitle', 'Track and analyze employee attendance metrics')

@section('content')
<div class="container-fluid px-0">
    
    <!-- Hero Strip Section -->
    <div class="page-hero-strip mb-4">
        <div class="strip-rule"></div>
        <h2>Attendance Analytics</h2>
        <p>Comprehensive attendance report for {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</p>
    </div>

    <!-- Filter Card - Organic Glass -->
    <div class="card mb-4 fade-in-up" style="animation-delay: 0.05s;">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-sliders-h"></i>
            </div>
            Filter Report
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.attendance.report') }}" class="row g-3">
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
                        <i class="fas fa-building me-1"></i> Department
                    </label>
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-chart-line me-2"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                        <i class="fas fa-user-check me-1"></i> Mark / Update Attendance
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="exportReport()">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
                <div class="input-group" style="width: 260px;">
                    <span class="input-group-text bg-white border-0" style="border-radius: 50px 0 0 50px;">
                        <i class="fas fa-search" style="color: var(--gl);"></i>
                    </span>
                    <input type="text" id="tableSearch" class="form-control" placeholder="Search employee..." style="border-radius: 0 50px 50px 0;">
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats Cards - Matching Admin Layout -->
    <div class="row g-4 mb-4 stagger-children">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-info">
                    <p>Present Days</p>
                    <h3>{{ array_sum(array_column($report, 'present')) }}</h3>
                    <span class="stats-trend up">
                        <i class="fas fa-leaf"></i> Total attendance
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #7a1f1f 0%, #5a1010 100%);">
                <div class="stats-icon">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="stats-info">
                    <p>Absent Days</p>
                    <h3>{{ array_sum(array_column($report, 'absent')) }}</h3>
                    <span class="stats-trend">
                        <i class="fas fa-clock"></i> Missing attendance
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #b86b1a 0%, #8a4e0a 100%);">
                <div class="stats-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stats-info">
                    <p>Late Arrivals</p>
                    <h3>{{ array_sum(array_column($report, 'late')) }}</h3>
                    <span class="stats-trend">
                        <i class="fas fa-exclamation-triangle"></i> Delayed check-ins
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card lime-card">
                <div class="stats-icon">
                    <i class="fas fa-charging-station"></i>
                </div>
                <div class="stats-info">
                    <p>Overtime Hours</p>
                    <h3>{{ number_format(array_sum(array_column($report, 'overtime')), 1) }}</h3>
                    <span class="stats-trend up">
                        <i class="fas fa-fire"></i> Extra work hours
                    </span>
                </div>
            </div>
        </div>
    <!-- Daily Attendance Log Table (with 12-Hr AM/PM Seconds & Exact Durations) -->
    <div class="card mb-4 fade-in-up" style="animation-delay: 0.1s;">
        <div class="card-header bg-white fw-bold d-flex align-items-center justify-content-between">
            <span><i class="fas fa-clock text-primary me-2"></i> Daily Time Tracking Log for {{ $date->format('M d, Y (l)') }}</span>
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                <i class="fas fa-plus me-1"></i> Mark / Update Today's Attendance
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Allocated Shift & Timings</th>
                            <th>Check-In (Entry Time)</th>
                            <th>Check-Out (Exit Time)</th>
                            <th>Tea Break</th>
                            <th>Lunch Break</th>
                            <th>Overtime (Xh Ym Zs)</th>
                            <th>Net Work Hours</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                        @php
                            $shiftName = $att->workShift ? $att->workShift->name : 'General Shift';
                            $shiftStart = $att->workShift ? \Carbon\Carbon::parse($att->workShift->start_time)->format('g:i A') : '9:00 AM';
                            $shiftEnd = $att->workShift ? \Carbon\Carbon::parse($att->workShift->end_time)->format('g:i A') : '6:00 PM';
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $att->employee->name ?? 'Employee' }}</div>
                                <small class="text-muted"><code>{{ $att->employee->employee_code ?? '' }}</code> • {{ $att->employee->department ?? '' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-primary" style="font-size: 0.85rem;">{{ $shiftName }}</div>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i> {{ $shiftStart }} - {{ $shiftEnd }}</small>
                            </td>
                            <td>
                                @if($att->check_in)
                                    <span class="badge bg-light text-dark border font-monospace">
                                        <i class="fas fa-sign-in-alt text-success me-1"></i> {{ \Carbon\Carbon::parse($att->check_in)->format('h:i:s A') }}
                                    </span>
                                @else
                                    <span class="text-muted small">--:--:--</span>
                                @endif
                            </td>
                            <td>
                                @if($att->check_out)
                                    <span class="badge bg-light text-dark border font-monospace">
                                        <i class="fas fa-sign-out-alt text-danger me-1"></i> {{ \Carbon\Carbon::parse($att->check_out)->format('h:i:s A') }}
                                    </span>
                                @else
                                    <span class="text-muted small">--:--:--</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-dark small">{{ $att->formatted_break_time }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark small">{{ $att->formatted_lunch_time }}</span>
                            </td>
                            <td>
                                <span class="badge bg-indigo text-indigo border fw-bold" style="color: #4338ca; background-color: #e0e7ff;">
                                    <i class="fas fa-bolt me-1"></i> {{ $att->formatted_overtime }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success text-white border fw-bold">
                                    <i class="fas fa-stopwatch me-1"></i> {{ $att->formatted_working_hours }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($att->status === 'present')
                                    <span class="badge bg-success">PRESENT</span>
                                @elseif($att->status === 'late')
                                    @php
                                        $lh = floor($att->late_minutes / 60);
                                        $lm = $att->late_minutes % 60;
                                        $lateStr = ($lh > 0 && $lm > 0) ? "{$lh}h {$lm}m" : ($lh > 0 ? "{$lh}h" : "{$lm}m");
                                    @endphp
                                    <span class="badge bg-warning text-dark">⏰ LATE {{ $lateStr }}</span>
                                @elseif($att->status === 'absent')
                                    <span class="badge bg-danger">ABSENT</span>
                                @elseif($att->status === 'half_day')
                                    <span class="badge bg-secondary">HALF DAY</span>
                                @else
                                    <span class="badge bg-info text-dark">{{ strtoupper($att->status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                                    Edit Status
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                No attendance entries logged for {{ $date->format('M d, Y') }} yet. Use "+ Mark / Update Today's Attendance" button above.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($attendances->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>

    <!-- Main Report Table -->
    <div class="card fade-in-up" style="animation-delay: 0.15s;">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-table"></i>
            </div>
            Monthly Attendance Summary
            <div class="ms-auto">
                <span class="badge bg-gradient-success">
                    <i class="fas fa-users me-1"></i> {{ count($report) }} Employees
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="reportTable">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Late</th>
                            <th class="text-center">Half Day</th>
                            <th class="text-center">Overtime</th>
                            <th class="text-center">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report as $data)
                        @php
                            $totalDays = $data['present'] + $data['absent'] + $data['late'];
                            $percentage = $totalDays > 0 ? round(($data['present'] / $totalDays) * 100, 1) : 0;
                            
                            $badgeClass = $percentage >= 90 ? 'badge-lime' : 
                                         ($percentage >= 75 ? 'badge-green' : 
                                         ($percentage >= 50 ? 'badge-gold' : 'badge-red'));
                        @endphp
                        <tr class="align-middle">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                        {{ strtoupper(substr($data['employee']->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--gd);">
                                            {{ $data['employee']->name }}
                                        </div>
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            <code>{{ $data['employee']->employee_code }}</code>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-green">
                                    {{ $data['employee']->department }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold" style="color: var(--gb); font-size: 1.1rem;">
                                    {{ $data['present'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold" style="color: #c62828;">
                                    {{ $data['absent'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold" style="color: #ef6c00;">
                                    {{ $data['late'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-gold">{{ $data['half_day'] }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <i class="fas fa-clock" style="color: var(--gl); font-size: 0.7rem;"></i>
                                    <span class="fw-semibold">{{ number_format($data['overtime'], 1) }}</span>
                                    <small class="text-muted">hrs</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="badge {{ $badgeClass }}" style="font-size: 0.75rem;">
                                        {{ $percentage }}%
                                    </span>
                                    <div class="progress" style="width: 60px; height: 3px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $percentage }}%; background: var(--gl);" 
                                             aria-valuenow="{{ $percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No attendance data available for the selected period.</p>
                                <small class="text-muted">Try adjusting your filters or select a different month/year</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-chart-simple" style="color: var(--gl);"></i>
                        <span class="text-muted small">Overall Attendance:</span>
                        @php
                            $totalPresent = array_sum(array_column($report, 'present'));
                            $totalDaysCount = $totalPresent + array_sum(array_column($report, 'absent')) + array_sum(array_column($report, 'late'));
                            $overallPercentage = $totalDaysCount > 0 ? round(($totalPresent / $totalDaysCount) * 100, 1) : 0;
                        @endphp
                        <span class="fw-bold" style="color: var(--gb);">{{ $overallPercentage }}%</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge badge-lime">
                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Excellent (≥90%)
                    </span>
                    <span class="badge badge-green">
                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Good (≥75%)
                    </span>
                    <span class="badge badge-gold">
                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Average (≥50%)
                    </span>
                    <span class="badge badge-red">
                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Poor (&lt;50%)
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Additional custom styles matching admin layout */
    .progress {
        background-color: rgba(126, 213, 58, 0.15);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 10px;
    }
    
    /* Table row hover effect */
    .table-hover tbody tr:hover {
        background: rgba(126, 213, 58, 0.04);
    }
    
    /* Card footer styling */
    .card-footer {
        border-top: 1px solid rgba(126, 213, 58, 0.15);
    }
    
    /* Search input styling */
    .input-group-text {
        background: rgba(255, 255, 255, 0.9);
        border: 1.5px solid rgba(126, 213, 58, 0.25);
        border-right: none;
    }
    
    .input-group .form-control {
        border-left: none;
    }
    
    .input-group .form-control:focus {
        border-left: none;
        box-shadow: none;
    }
    
    /* Animation delays for stats cards */
    .stagger-children > *:nth-child(1) { animation-delay: 0.05s; }
    .stagger-children > *:nth-child(2) { animation-delay: 0.12s; }
    .stagger-children > *:nth-child(3) { animation-delay: 0.19s; }
    .stagger-children > *:nth-child(4) { animation-delay: 0.26s; }
    
    /* Print styles */
    @media print {
        .sidebar, .topbar, .card-header .ms-auto, 
        .card-footer, .btn, .input-group, .page-hero-strip {
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
        
        .stats-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Table search functionality
        $('#tableSearch').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#reportTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        
        // Animate progress bars on load
        $('.progress-bar').each(function() {
            var $this = $(this);
            var width = $this.css('width');
            $this.css('width', '0');
            setTimeout(function() {
                $this.css('width', width);
            }, 300);
        });
    });
    
    function exportReport() {
        const month = $('select[name="month"]').val();
        const year = $('select[name="year"]').val();
        const department = $('select[name="department"]').val();
        
        Swal.fire({
            title: 'Exporting Report',
            text: 'Please wait while we prepare your Excel file...',
            allowOutsideClick: false,
            background: 'rgba(255,255,255,0.95)',
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        window.location.href = "{{ route('admin.attendance.export') }}?month=" + month + "&year=" + year + "&department=" + department;
        
        setTimeout(() => {
            Swal.close();
            showToast('Report exported successfully!', 'success');
        }, 2000);
    }
</script>
@endpush

{{-- MODAL: Mark Single Employee Attendance --}}
<div class="modal fade" id="markAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.attendance.mark') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-check text-primary me-2"></i> Mark / Update Attendance Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Employee</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">-- Choose Employee --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Attendance Status</label>
                    <select name="status" class="form-select" required>
                        <option value="present">Present</option>
                        <option value="absent" selected>Absent</option>
                        <option value="late">Late</option>
                        <option value="half_day">Half Day</option>
                        <option value="holiday">Holiday</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small"><i class="fas fa-stopwatch text-success me-1"></i> Check In Time (12-Hr AM/PM)</label>
                        <div class="input-group">
                            <select name="check_in_hour" class="form-select">
                                <option value="">-- HH --</option>
                                @for($h=1; $h<=12; $h++)
                                    <option value="{{ sprintf('%02d', $h) }}" {{ $h == 9 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                                @endfor
                            </select>
                            <span class="input-group-text">:</span>
                            <select name="check_in_min" class="form-select">
                                @for($m=0; $m<60; $m+=5)
                                    <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                                @endfor
                            </select>
                            <select name="check_in_ampm" class="form-select fw-bold text-primary">
                                <option value="AM" selected>AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small"><i class="fas fa-stopwatch text-danger me-1"></i> Check Out Time (12-Hr AM/PM)</label>
                        <div class="input-group">
                            <select name="check_out_hour" class="form-select">
                                <option value="">-- HH --</option>
                                @for($h=1; $h<=12; $h++)
                                    <option value="{{ sprintf('%02d', $h) }}" {{ $h == 6 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                                @endfor
                            </select>
                            <span class="input-group-text">:</span>
                            <select name="check_out_min" class="form-select">
                                @for($m=0; $m<60; $m+=5)
                                    <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                                @endfor
                            </select>
                            <select name="check_out_ampm" class="form-select fw-bold text-danger">
                                <option value="AM">AM</option>
                                <option value="PM" selected>PM</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small"><i class="fas fa-mug-hot text-warning me-1"></i> Tea Break (Mins)</label>
                        <input type="number" name="tea_break_mins" class="form-control" min="0" placeholder="e.g. 15">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small"><i class="fas fa-hamburger text-orange me-1"></i> Lunch (Mins)</label>
                        <input type="number" name="lunch_break_mins" class="form-control" min="0" placeholder="e.g. 45">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small"><i class="fas fa-fire text-danger me-1"></i> Overtime (Hrs)</label>
                        <input type="number" name="overtime_hours" class="form-control" min="0" step="0.1" placeholder="e.g. 1.5">
                    </div>
                </div>

                <div class="alert alert-warning py-2 mb-3 small" style="border-radius: 8px;">
                    <i class="fas fa-exclamation-triangle me-1"></i> <strong>Safety Guard:</strong> If an employee has already clocked in or worked on the selected date, marking them <em>Absent</em> is automatically blocked to prevent accidental overwrites.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Remarks / Reason</label>
                    <input type="text" name="remarks" class="form-control" placeholder="e.g. Uninformed Absence / Approved Leave">
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="force_absent" value="1" id="forceAbsentCheck">
                    <label class="form-check-label text-danger small fw-semibold" for="forceAbsentCheck">
                        Force Overwrite: Override existing clock-in data and force status to Absent
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Attendance Status</button>
            </div>
        </form>
    </div>
@endsection
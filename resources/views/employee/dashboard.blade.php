@extends('layouts.admin')

@section('title', 'Employee Dashboard — ' . $employee->name)
@section('page-title', 'Employee Workspace')
@section('page-subtitle', 'Welcome back, ' . $employee->name . ' — Manage your attendance, assigned projects, and leaves')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px; background: #eaf6ed; color: #0d3b1a;">
            <i class="fas fa-check-circle me-2 text-success"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- HERO STRIP: Employee Profile & Identification --}}
    <div class="card border-0 shadow-sm mb-4 text-white overflow-hidden" 
         style="border-radius: 18px; background: linear-gradient(135deg, #0d3b1a 0%, #175429 55%, #1e7c42 100%);">
        <div class="card-body p-4 p-md-5 position-relative">
            {{-- Background decorative leaf glow --}}
            <div style="position: absolute; right: -20px; top: -30px; width: 220px; height: 220px; border-radius: 50%; background: radial-gradient(circle, rgba(126, 213, 58, 0.18) 0%, transparent 70%); pointer-events: none;"></div>

            <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3 gap-md-4 flex-wrap flex-sm-nowrap">
                        {{-- Avatar --}}
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow flex-shrink-0"
                             style="width: 72px; height: 72px; background: rgba(255, 255, 255, 0.15); border: 2px solid rgba(255, 255, 255, 0.3); font-size: 1.6rem; backdrop-filter: blur(5px);">
                            @if($employee->profile_photo)
                                <img src="{{ Storage::url($employee->profile_photo) }}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                            @else
                                {{ strtoupper(substr($employee->name, 0, 2)) }}
                            @endif
                        </div>

                        {{-- Name & Identification Badges --}}
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h3 class="fw-bold mb-0 text-white" style="letter-spacing: -0.5px;">{{ $employee->name }}</h3>
                                <span class="badge bg-white text-success fw-bold font-monospace shadow-sm" style="font-size: 0.85rem; padding: 5px 10px; border-radius: 8px;">
                                    <i class="fas fa-id-badge me-1"></i> Emp Code: {{ $employee->employee_code }}
                                </span>
                                @if($employee->isTeamLeader())
                                    <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.75rem;">
                                        <i class="fas fa-crown me-1"></i> Team Leader
                                    </span>
                                @endif
                            </div>
                            <div class="text-white-50 small mb-2">
                                <i class="fas fa-briefcase me-1"></i> {{ $employee->designation ?? 'Software Engineer' }} &bull; 
                                <i class="fas fa-building ms-2 me-1"></i> {{ $employee->department ?? 'Engineering' }}
                            </div>
                            <div class="d-flex align-items-center gap-3 text-white-50 small flex-wrap">
                                <span><i class="far fa-envelope me-1 text-white"></i> {{ $employee->email }}</span>
                                <span><i class="fas fa-phone-alt me-1 text-white"></i> {{ $employee->phone ?? '-' }}</span>
                                <span><i class="far fa-calendar-check me-1 text-white"></i> Joined: {{ optional($employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date) : null)->format('d M Y') ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a href="{{ route('employee.assigned-leads.index') }}" 
                           class="btn btn-light fw-bold shadow-sm" style="color: #0d3b1a; border-radius: 10px;">
                            <i class="fas fa-tasks text-success me-1"></i> My Assigned Projects
                        </a>
                        <a href="{{ route('employee.leaves.create') }}" 
                           class="btn btn-outline-light fw-semibold" style="border-radius: 10px;">
                            <i class="far fa-calendar-plus me-1"></i> Apply Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI METRICS CARDS --}}
    <div class="row g-3 mb-4">
        {{-- 1. Attendance Today --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: {{ $todayAttendance ? '#10b981' : '#f59e0b' }};">
                        <i class="fas {{ $todayAttendance ? 'fa-fingerprint' : 'fa-clock' }} fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Today's Attendance</div>
                        @if($todayAttendance)
                            <div class="fw-bold text-success fs-6">
                                Present ({{ optional($todayAttendance->check_in)->format('h:i A') ?? 'Checked-In' }})
                            </div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Out: {{ optional($todayAttendance->check_out)->format('h:i A') ?? 'In Office' }}</div>
                        @else
                            <div class="fw-bold text-warning fs-6">Not Punched In</div>
                            <form action="{{ route('employee.attendance.check-in') }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline-success py-0 px-2 fw-bold" style="font-size: 0.7rem;">
                                    <i class="fas fa-sign-in-alt me-1"></i> Clock In Now
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Monthly Working Days --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: #2563eb;">
                        <i class="fas fa-calendar-alt fs-5"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">This Month's Days</div>
                        <div class="fw-bold text-dark fs-5">{{ $monthlyAttendance }} <span class="fs-6 fw-normal text-muted">Days</span></div>
                        <div class="text-muted small" style="font-size: 0.72rem;">{{ now()->format('F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Assigned Leads / Project Deliverables --}}
        @php
            $activeAssignedCount = $employee->workerAssignments()->whereIn('status', ['assigned', 'in_progress'])->count();
            $completedAssignedCount = $employee->workerAssignments()->where('status', 'completed')->count();
        @endphp
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: #8b5cf6;">
                        <i class="fas fa-project-diagram fs-5"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Assigned Projects</div>
                        <div class="fw-bold text-dark fs-5">{{ $activeAssignedCount }} <span class="fs-6 fw-normal text-muted">Active</span></div>
                        <div class="text-muted small" style="font-size: 0.72rem;">{{ $completedAssignedCount }} completed deliverables</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Leave Balance --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: #0d9488;">
                        <i class="fas fa-umbrella-beach fs-5"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Leave Balance</div>
                        @if($leaveBalance)
                            @php
                                $sickLeft = max(0, $leaveBalance->sick_leave_total - $leaveBalance->sick_leave_used);
                                $casualLeft = max(0, $leaveBalance->casual_leave_total - $leaveBalance->casual_leave_used);
                                $annualLeft = max(0, $leaveBalance->annual_leave_total - $leaveBalance->annual_leave_used);
                            @endphp
                            <div class="fw-bold text-dark fs-6">{{ $sickLeft + $casualLeft + $annualLeft }} Days Left</div>
                            <div class="text-muted small" style="font-size: 0.72rem;">
                                Sick: {{ $sickLeft }} &bull; Casual: {{ $casualLeft }} &bull; Annual: {{ $annualLeft }}
                            </div>
                        @else
                            <div class="fw-bold text-dark fs-6">12 Days Available</div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Standard annual allotment</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN DASHBOARD SPLIT: Assigned Projects vs Timesheet & Payslips --}}
    <div class="row g-4">
        {{-- LEFT COLUMN: Assigned Lead Deliverables from Team Leader --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <span class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #0d3b1a 0%, #1e7c42 100%); font-size: 0.8rem;">
                            <i class="fas fa-tasks"></i>
                        </span>
                        Assigned Project Leads & Deliverables
                    </h6>
                    <a href="{{ route('employee.assigned-leads.index') }}" class="btn btn-sm btn-outline-success" style="border-radius: 8px;">
                        View All ({{ $employee->workerAssignments()->count() }}) &rarr;
                    </a>
                </div>
                <div class="card-body p-0">
                    @php
                        $recentLeadAssignments = $employee->workerAssignments()
                            ->with(['lead.teamLeader'])
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp

                    @if($recentLeadAssignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th class="ps-3">Project / Lead</th>
                                        <th>Team Leader</th>
                                        <th>Timeline & Deadline</th>
                                        <th>Status</th>
                                        <th class="text-center pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentLeadAssignments as $task)
                                        @php
                                            $lead = $task->lead;
                                            $taskBadge = $task->status_badge;
                                        @endphp
                                        <tr>
                                            <td class="ps-3 py-3">
                                                <div class="fw-bold text-dark">{{ $lead ? $lead->name : 'Project Task' }}</div>
                                                <div class="small text-muted">
                                                    <span class="badge bg-light text-dark border me-1">{{ $lead->domain ?? 'General' }}</span>
                                                    {{ $lead->service_needed ?? 'Standard Project' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($lead && $lead->teamLeader)
                                                    <div class="fw-bold text-dark small">{{ $lead->teamLeader->name }}</div>
                                                    @if($lead->teamLeader->employee_code)
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace" style="font-size: 0.68rem;">
                                                            {{ $lead->teamLeader->employee_code }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted small">Assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-dark">
                                                    <i class="far fa-calendar-alt text-success me-1"></i>
                                                    {{ optional($task->start_date)->format('d M') }} &rarr; {{ optional($task->end_date)->format('d M Y') }}
                                                </div>
                                                @if($task->is_overdue)
                                                    <span class="badge bg-danger text-white" style="font-size: 0.65rem;">Overdue</span>
                                                @elseif($task->days_left !== null && !in_array($task->status, ['completed', 'submitted']))
                                                    <span class="badge bg-info text-white" style="font-size: 0.65rem;">{{ $task->days_left }}d left</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $taskBadge['class'] }}" style="font-size: 0.72rem;">
                                                    <i class="{{ $taskBadge['icon'] }} me-1"></i> {{ $taskBadge['label'] }}
                                                </span>
                                            </td>
                                            <td class="text-center pe-3">
                                                <a href="{{ route('employee.assigned-leads.index') }}" 
                                                   class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem; border-radius: 8px;">
                                                    <i class="fas fa-external-link-alt me-1"></i> Open Task
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-clipboard-check fa-3x text-muted mb-3 opacity-25"></i>
                            <h6 class="fw-bold text-dark">No Active Lead Assignments</h6>
                            <p class="small text-muted mb-0">When your Team Leader forwards and assigns a project to you, it will appear right here with milestones and submission checklists.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timesheet Logs --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="far fa-clock text-primary me-2"></i> Recent Work Timesheets
                    </h6>
                    <a href="{{ route('employee.time-sheets.create') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                        <i class="fas fa-plus me-1"></i> Log Hours
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentTimeSheets) && $recentTimeSheets->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentTimeSheets as $ts)
                                <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $ts->project ? $ts->project->name : ($ts->task_title ?? 'Daily Task') }}</div>
                                        <div class="small text-muted">{{ optional($ts->date)->format('d M Y') }} &bull; {{ $ts->description }}</div>
                                    </div>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $ts->hours }} hrs</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-muted small">
                            No recent timesheets logged for this week.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Quick Profile, Payslips & Leave Status --}}
        <div class="col-lg-5">
            {{-- DLK Corporate Card --}}
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-id-card text-success me-2"></i> Employee Identification Card
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 rounded-3 mb-3 border text-center" 
                         style="background: linear-gradient(135deg, rgba(13, 59, 26, 0.05) 0%, rgba(30, 124, 66, 0.08) 100%); border-color: rgba(30, 124, 66, 0.2) !important;">
                        <div class="small text-muted text-uppercase fw-bold mb-1" style="letter-spacing: 1px;">Official Employee Code</div>
                        <div class="fs-4 fw-bold font-monospace text-success mb-1">{{ $employee->employee_code }}</div>
                        <span class="badge bg-success bg-opacity-20 text-success border border-success">
                            Verified Member of DLK Groups
                        </span>
                    </div>

                    <div class="list-group list-group-flush small">
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Full Name</span>
                            <span class="fw-bold text-dark">{{ $employee->name }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Corporate Email</span>
                            <span class="fw-semibold text-dark">{{ $employee->email }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Department</span>
                            <span class="fw-semibold text-dark">{{ $employee->department ?? 'General' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Designation</span>
                            <span class="fw-semibold text-dark">{{ $employee->designation ?? 'Staff' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Employment Type</span>
                            <span class="badge bg-light text-dark border">{{ ucwords(str_replace('_', ' ', $employee->employment_type ?? 'Full Time')) }}</span>
                        </div>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('employee.profile') }}" class="btn btn-sm btn-outline-success w-100 fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-user-edit me-1"></i> Edit Profile & Emergency Contact
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent Payslips Card --}}
            <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-file-invoice-dollar text-success me-2"></i> Recent Payslips
                    </h6>
                    <a href="{{ route('employee.payslips') }}" class="small text-success text-decoration-none fw-semibold">
                        View All &rarr;
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentPayslips) && $recentPayslips->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPayslips as $slip)
                                <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark small">{{ date("F", mktime(0, 0, 0, $slip->month, 1)) }} {{ $slip->year }}</div>
                                        <div class="text-success small fw-bold">₹{{ number_format($slip->net_salary, 2) }}</div>
                                    </div>
                                    <a href="{{ route('employee.payslips.download', $slip) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;" title="Download PDF Payslip">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-muted small">
                            No published payslips available yet.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Leave Requests Status --}}
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-plane-departure text-warning me-2"></i> Leaves Summary
                    </h6>
                    <a href="{{ route('employee.leaves') }}" class="small text-success text-decoration-none fw-semibold">
                        Leave History &rarr;
                    </a>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">Pending Approval</div>
                                <h4 class="fw-bold text-warning mb-0">{{ $pendingLeaves ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">Approved This Year</div>
                                <h4 class="fw-bold text-success mb-0">{{ $approvedLeaves ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

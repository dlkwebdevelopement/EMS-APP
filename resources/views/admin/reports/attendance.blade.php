{{-- resources/views/admin/reports/attendance.blade.php --}}
@extends('layouts.admin')

@section('title', 'Attendance Report')

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-calendar-check me-2"></i>
                Attendance Report — {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
            </h5>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.reports.attendance') }}">
                    @csrf
                    <input type="hidden" name="month"  value="{{ $month }}">
                    <input type="hidden" name="year"   value="{{ $year }}">
                    <input type="hidden" name="export" value="csv">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Late</th>
                            <th class="text-center">Half Day</th>
                            <th class="text-center">Holiday</th>
                            <th class="text-center">Overtime (hrs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report as $row)
                        <tr>
                            <td>
                                <strong>{{ $row['employee']->name }}</strong><br>
                                <small class="text-muted">{{ $row['employee']->employee_code }}</small>
                            </td>
                            <td>{{ $row['employee']->department }}</td>
                            <td class="text-center"><span class="badge bg-success">{{ $row['present'] }}</span></td>
                            <td class="text-center"><span class="badge bg-danger">{{ $row['absent'] }}</span></td>
                            <td class="text-center"><span class="badge bg-warning text-dark">{{ $row['late'] }}</span></td>
                            <td class="text-center"><span class="badge bg-info text-dark">{{ $row['half_day'] }}</span></td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $row['holiday'] }}</span></td>
                            <td class="text-center">{{ $row['overtime'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No attendance data found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
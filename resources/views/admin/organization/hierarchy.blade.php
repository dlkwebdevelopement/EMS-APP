@extends('layouts.admin')

@section('title', 'Organization Hierarchy')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800">Organization Hierarchy</h1>
            <p class="text-muted mb-0">View company department structures, reporting managers, and team hierarchies</p>
        </div>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-users mr-1"></i> Employee Directory
        </a>
    </div>

    <!-- Departments Summary Cards -->
    <div class="row mb-4">
        @forelse($departments as $dept)
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ $dept->code ?? 'DEPT' }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dept->name }}</div>
                                <small class="text-muted">
                                    Manager: {{ $dept->manager->name ?? 'Unassigned' }}
                                </small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No departments configured yet.</div>
            </div>
        @endforelse
    </div>

    <!-- Organization Tree -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Management & Team Reporting Structure</h6>
        </div>
        <div class="card-body">
            @if($topLevelEmployees->count() > 0)
                <div class="org-tree">
                    @foreach($topLevelEmployees as $emp)
                        <div class="org-node mb-4 p-3 border rounded bg-light">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:45px; height:45px; font-weight:bold;">
                                        {{ strtoupper(substr($emp->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">{{ $emp->name }}</h5>
                                        <span class="badge badge-info">{{ $emp->designation ?? 'Manager' }}</span>
                                        <span class="badge badge-secondary ml-1">{{ $emp->department ?? 'General' }}</span>
                                        <small class="text-muted d-block mt-1">{{ $emp->email }} • Code: {{ $emp->employee_code }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('admin.employees.show', $emp->id) }}" class="btn btn-sm btn-outline-primary">
                                    View Profile
                                </a>
                            </div>

                            @if($emp->directReports->count() > 0)
                                <div class="ml-4 pl-4 border-left mt-3">
                                    <h6 class="text-xs font-weight-bold text-uppercase text-muted mb-2">Direct Reports ({{ $emp->directReports->count() }})</h6>
                                    <div class="row">
                                        @foreach($emp->directReports as $report)
                                            <div class="col-md-6 mb-2">
                                                <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <strong class="d-block text-gray-900">{{ $report->name }}</strong>
                                                        <small class="text-muted">{{ $report->designation ?? 'Team Member' }}</small>
                                                    </div>
                                                    <span class="badge badge-{{ $report->employment_status === 'active' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($report->employment_status ?? 'active') }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">No top-level organization structure found. Assign reporting managers to employees in employee edit screen.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- resources/views/employee/time-sheets/create.blade.php --}}
@extends('layouts.employee')

@section('title', 'Add Time Sheet')
@section('page-title', 'Add Time Sheet')
@section('page-subtitle', 'Record your daily work hours and tasks')

@section('content')
<div class="container-fluid px-0">

    <div class="card">
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-clock"></i>
            </div>
            New Time Sheet Entry
        </div>
        <div class="card-body">
            <form action="{{ route('employee.time-sheets.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                               value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Project (Optional)</label>
                        <input type="text" name="project" class="form-control" 
                               placeholder="Project name or code" value="{{ old('project') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                               value="{{ old('start_time', '09:00') }}" required>
                        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">End Time <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" 
                               value="{{ old('end_time', '18:00') }}" required>
                        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Break Hours <span class="text-danger">*</span></label>
                        <input type="number" step="0.5" name="break_hours" class="form-control @error('break_hours') is-invalid @enderror" 
                               value="{{ old('break_hours', 1) }}" required>
                        <small class="text-muted">Enter break time in hours (e.g., 0.5 for 30 minutes)</small>
                        @error('break_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Task Description <span class="text-danger">*</span></label>
                        <textarea name="task_description" class="form-control @error('task_description') is-invalid @enderror" 
                                  rows="4" required placeholder="Describe the tasks you worked on...">{{ old('task_description') }}</textarea>
                        @error('task_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="alert alert-info mt-4" style="background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.2); border-radius: var(--r-pill);">
                    <i class="fas fa-info-circle me-2" style="color: #0ea5e9;"></i>
                    Total hours will be calculated automatically: (End Time - Start Time - Break Hours)
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Submit Time Sheet
                    </button>
                    <a href="{{ route('employee.time-sheets.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
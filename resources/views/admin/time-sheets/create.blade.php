{{-- resources/views/admin/time-sheets/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Add Time Sheet')
@section('page-title', 'Create Time Sheet')
@section('page-subtitle', 'Manually record work hours for an employee')

@section('content')
<div class="container-fluid px-0">

    <div class="row g-4 justify-content-center">
        <div class="col-xl-8">
            <div class="card fade-in-up">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    New Time Sheet Entry
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.time-sheets.store') }}" method="POST">
                        @csrf

                        <div class="organic-rule"></div>
                        <h5 class="mb-4" style="color: var(--gd); font-family: 'Playfair Display', serif;">Entry Details</h5>

                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

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
                                @error('break_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Task Description <span class="text-danger">*</span></label>
                                <textarea name="task_description" class="form-control @error('task_description') is-invalid @enderror" 
                                          rows="4" required placeholder="Describe the work performed...">{{ old('task_description') }}</textarea>
                                @error('task_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="glass-panel p-3 mt-4" style="background: rgba(126, 213, 58, 0.05); border-color: rgba(126, 213, 58, 0.2);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-info-circle" style="color: var(--gb);"></i>
                                <span class="small text-muted">Total hours will be calculated as: (End Time - Start Time - Break Hours)</span>
                            </div>
                        </div>

                        <div class="mt-5 d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-check-circle me-1"></i> Save Entry
                            </button>
                            <a href="{{ route('admin.time-sheets.index') }}" class="btn btn-outline-primary px-4">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
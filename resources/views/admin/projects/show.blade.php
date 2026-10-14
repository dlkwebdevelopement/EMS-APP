@extends('layouts.admin')

@section('title', 'Project Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Project: {{ $project->title }}</h2>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h4>General Info</h4>
            <p><strong>Client:</strong> {{ $project->client->name ?? 'N/A' }}</p>
            <p><strong>Program / Stack:</strong> {{ $project->program_tech_stack }}</p>
            <p><strong>Site Link:</strong> <a href="{{ $project->site_link }}" target="_blank">{{ $project->site_link }}</a></p>
            <p><strong>Full Details:</strong> <br> {!! nl2br(e($project->full_details)) !!}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h4>Technical Sections</h4>
                    <p><strong>Frontend:</strong> <br> {!! nl2br(e($project->frontend_section)) !!}</p>
                    <p><strong>Backend:</strong> <br> {!! nl2br(e($project->backend_section)) !!}</p>
                    <p><strong>Database:</strong> <br> {!! nl2br(e($project->database)) !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h4>Hosting & Credentials</h4>
                    <p><strong>Server Host Details:</strong> <br> {!! nl2br(e($project->server_host_details)) !!}</p>
                    <p class="text-danger"><strong>Credentials:</strong> <br> {!! nl2br(e($project->credentials_details)) !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4>Assigned Employees</h4>
            <ul>
                @forelse($project->employees as $employee)
                    <li>{{ $employee->name }} ({{ $employee->email }})</li>
                @empty
                    <li>No employees assigned to this project.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

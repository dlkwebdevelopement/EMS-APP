{{-- resources/views/admin/payroll/details.blade.php --}}
@extends('layouts.admin')

@section('title', 'Payroll Details')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-money-bill-wave me-2"></i>Payroll Details
        </h1>
        <div>
            <a href="{{ route('admin.payroll.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @include('admin.payroll.show', ['payroll' => $payroll])
        </div>
    </div>
</div>
@endsection
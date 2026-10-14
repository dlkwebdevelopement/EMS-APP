@extends('layouts.admin')

@section('title', 'Payroll Summary')
@section('page-title', 'Payroll Summary')

@section('content')
<div class="row g-4 stagger-children">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon"><i class="fas fa-users"></i></div>
            <div class="stats-info">
                <p>Total Records</p>
                <h3>{{ $stats['total_processed'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card lime-card">
            <div class="stats-icon"><i class="fas fa-rupee-sign"></i></div>
            <div class="stats-info">
                <p>Total Payout</p>
                <h3>₹{{ number_format($stats['total_payout'], 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card teal-card">
            <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stats-info">
                <p>Paid</p>
                <h3>{{ $stats['paid_count'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <div class="stats-icon"><i class="fas fa-clock"></i></div>
            <div class="stats-info">
                <p>Pending</p>
                <h3>{{ $stats['pending_count'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4 fade-in-up">
    <div class="card-header">
        <div class="header-icon"><i class="fas fa-chart-bar"></i></div>
        Monthly Overview
    </div>
    <div class="card-body">
        <p class="text-muted">Detailed analytics for the current month's payroll processing.</p>
        <div class="d-flex justify-content-center py-5">
            <i class="fas fa-chart-pie fa-5x text-light"></i>
        </div>
    </div>
</div>
@endsection

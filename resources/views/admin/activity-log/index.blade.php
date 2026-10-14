{{-- resources/views/admin/activity-log/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('page-subtitle', 'Full audit trail of all system actions')

@section('content')
<div class="container-fluid px-0">

    {{-- ══ STAT CARDS ══ --}}
    <div class="row g-3 mb-4 stagger-children">
        <div class="col-6 col-md-3">
            <div class="al-stat-card">
                <div class="al-stat-icon" style="background:rgba(99,102,241,.1);color:#4338ca">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div>
                    <div class="al-stat-num">{{ number_format($stats['total_today']) }}</div>
                    <div class="al-stat-lbl">Today</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="al-stat-card">
                <div class="al-stat-icon" style="background:rgba(14,165,233,.1);color:#0369a1">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div>
                    <div class="al-stat-num">{{ number_format($stats['total_week']) }}</div>
                    <div class="al-stat-lbl">This Week</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="al-stat-card">
                <div class="al-stat-icon" style="background:rgba(22,163,74,.1);color:#166534">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <div class="al-stat-num">{{ number_format($stats['total_month']) }}</div>
                    <div class="al-stat-lbl">This Month</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="al-stat-card">
                <div class="al-stat-icon" style="background:rgba(245,158,11,.1);color:#b45309">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <div class="al-stat-num">{{ number_format($stats['admin_logs']) }}</div>
                    <div class="al-stat-lbl">Admin Actions</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ FILTER BAR ══ --}}
    <div class="card glass-card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.activity-log.index') }}" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="filter-label">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control border-start-0" placeholder="Search description…">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="filter-label">Action</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">All Actions</option>
                            @foreach($actionTypes as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="filter-label">User Type</label>
                        <select name="user_type" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            <option value="admin" {{ request('user_type') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="employee" {{ request('user_type') === 'employee' ? 'selected' : '' }}>Employee</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="filter-label">From Date</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="filter-label">To Date</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-forest w-100">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('admin.activity-log.index') }}" class="btn btn-sm btn-light w-100">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ LOG TABLE ══ --}}
    <div class="card glass-card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon" style="background:rgba(99,102,241,.1);">
                    <i class="fas fa-history" style="color:#4338ca"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">System Events</h5>
                    <small class="text-muted">{{ number_format($logs->total()) }} total entries</small>
                </div>
            </div>
            <span class="badge rounded-pill px-3 py-2" style="background:#eef2ff;color:#4338ca;font-size:.75rem;">
                Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table al-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:160px">Time</th>
                            <th style="width:90px">User</th>
                            <th style="width:130px">Action</th>
                            <th>Description</th>
                            <th style="width:120px">Module</th>
                            <th style="width:110px">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="al-row" data-id="{{ $log->id }}">
                                {{-- Time --}}
                                <td>
                                    <div class="fw-bold small text-dark">{{ $log->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $log->created_at->format('h:i:s A') }}</small>
                                    <div class="al-time-ago">{{ $log->created_at->diffForHumans() }}</div>
                                </td>

                                {{-- User --}}
                                <td>
                                    @php
                                        $uName = optional($log->user)->name ?? '—';
                                        $uType = $log->user_type ?? 'system';
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="al-avatar {{ $uType === 'admin' ? 'al-avatar-admin' : 'al-avatar-emp' }}">
                                            {{ strtoupper(substr($uName, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="small fw-semibold text-dark lh-1">{{ Str::limit($uName, 12) }}</div>
                                            <span class="badge al-badge-type {{ $uType === 'admin' ? 'al-badge-admin' : 'al-badge-emp' }}">
                                                {{ ucfirst($uType) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Action --}}
                                <td>
                                    @php
                                        $actionKey = strtolower($log->action ?? '');
                                        $actionMeta = [
                                            'login'         => ['bg'=>'#dcfce7','color'=>'#166534','icon'=>'fa-sign-in-alt'],
                                            'logout'        => ['bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'fa-sign-out-alt'],
                                            'created'       => ['bg'=>'#dbeafe','color'=>'#1e40af','icon'=>'fa-plus-circle'],
                                            'updated'       => ['bg'=>'#fef9c3','color'=>'#854d0e','icon'=>'fa-pencil-alt'],
                                            'deleted'       => ['bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'fa-trash-alt'],
                                            'approved'      => ['bg'=>'#dcfce7','color'=>'#166534','icon'=>'fa-check-circle'],
                                            'rejected'      => ['bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'fa-times-circle'],
                                            'password_changed' => ['bg'=>'#fde8d8','color'=>'#9a3412','icon'=>'fa-lock'],
                                            'profile_updated'  => ['bg'=>'#e0f2fe','color'=>'#0369a1','icon'=>'fa-user-edit'],
                                            'assigned'      => ['bg'=>'#ede9fe','color'=>'#5b21b6','icon'=>'fa-user-tag'],
                                            'converted'     => ['bg'=>'#d1fae5','color'=>'#065f46','icon'=>'fa-exchange-alt'],
                                        ];
                                        $meta = $actionMeta[$actionKey] ?? ['bg'=>'#f3f4f6','color'=>'#374151','icon'=>'fa-circle'];
                                    @endphp
                                    <span class="al-action-badge"
                                          style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }};">
                                        <i class="fas {{ $meta['icon'] }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $log->action ?? 'event')) }}
                                    </span>
                                </td>

                                {{-- Description --}}
                                <td>
                                    <div class="small text-dark al-description">{{ $log->description }}</div>
                                    @if($log->changes && count($log->changes) > 0)
                                        <button class="btn al-changes-toggle" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#changes{{ $log->id }}">
                                            <i class="fas fa-code-branch me-1"></i>View changes
                                        </button>
                                        <div class="collapse mt-2" id="changes{{ $log->id }}">
                                            <div class="al-changes-box">
                                                @foreach($log->changes as $field => $values)
                                                    @if(is_array($values) && isset($values['old'], $values['new']))
                                                        <div class="al-change-row">
                                                            <span class="al-field-name">{{ ucfirst(str_replace('_',' ',$field)) }}</span>
                                                            <span class="al-old">{{ Str::limit((string) $values['old'], 40) ?: '—' }}</span>
                                                            <i class="fas fa-arrow-right al-arrow"></i>
                                                            <span class="al-new">{{ Str::limit((string) $values['new'], 40) ?: '—' }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                {{-- Module --}}
                                <td>
                                    @if($log->model_type)
                                        @php $modelShort = class_basename($log->model_type); @endphp
                                        <span class="al-module-badge">
                                            {{ $modelShort }}
                                            @if($log->model_id)
                                                <small>#{{ $log->model_id }}</small>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- IP --}}
                                <td>
                                    <span class="al-ip">{{ $log->ip_address ?? '—' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No activity logs found.</p>
                                    @if(request()->hasAny(['search','action','user_type','from','to']))
                                        <a href="{{ route('admin.activity-log.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                                            Clear filters
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="card-footer bg-transparent border-top-0 px-4 py-3">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
/* ══ Activity Log — Premium Styles ══ */

.al-stat-card {
    background: #fff;
    border-radius: 18px;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid rgba(0,0,0,.05);
    box-shadow: 0 4px 18px rgba(0,0,0,.04);
    transition: all .3s cubic-bezier(.175,.885,.32,1.275);
}
.al-stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,.08); }
.al-stat-icon {
    width: 46px; height: 46px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
.al-stat-num { font-size: 1.6rem; font-weight: 800; color: var(--gd); line-height: 1; }
.al-stat-lbl { font-size: .75rem; font-weight: 600; color: var(--txt-m); text-transform: uppercase; letter-spacing: .5px; }

.filter-label { font-size: .72rem; font-weight: 700; color: var(--txt-m); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; display: block; }

.btn-forest { background: var(--gd); color: #fff; border: none; }
.btn-forest:hover { background: var(--gm); color: #fff; }

/* Table */
.al-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.al-table thead tr { background: #fafafa; }
.al-table th {
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .8px; color: var(--txt-m);
    padding: 1rem 1.2rem; border-bottom: 1px solid rgba(0,0,0,.05);
}
.al-row { transition: background .2s; cursor: default; }
.al-row:hover { background: rgba(126,213,58,.04); }
.al-row td { padding: .9rem 1.2rem; border-bottom: 1px solid rgba(0,0,0,.03); vertical-align: middle; }

.al-time-ago { font-size: .65rem; color: #94a3b8; margin-top: 2px; }

.al-avatar {
    width: 30px; height: 30px; border-radius: 8px;
    font-size: .75rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.al-avatar-admin { background: #eef2ff; color: #4338ca; }
.al-avatar-emp   { background: #dcfce7; color: #166534; }

.al-badge-type { font-size: .6rem; padding: 1px 6px; border-radius: 6px; }
.al-badge-admin { background: #eef2ff; color: #4338ca; }
.al-badge-emp   { background: #dcfce7; color: #166534; }

.al-action-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: .25rem .6rem; border-radius: 20px;
    font-size: .68rem; font-weight: 700; white-space: nowrap;
}

.al-description { line-height: 1.5; max-width: 320px; }

.al-changes-toggle {
    font-size: .65rem; color: #4338ca; padding: 0; border: none; background: none;
    text-decoration: underline; cursor: pointer; margin-top: 4px;
}
.al-changes-toggle:hover { color: var(--gd); }

.al-changes-box {
    background: #f8fafc; border-radius: 8px;
    border: 1px solid rgba(0,0,0,.06); padding: .6rem .8rem;
}
.al-change-row {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    font-size: .7rem; padding: 3px 0;
    border-bottom: 1px solid rgba(0,0,0,.04);
}
.al-change-row:last-child { border-bottom: none; }
.al-field-name { font-weight: 700; color: var(--txt-m); min-width: 90px; }
.al-old { color: #dc2626; text-decoration: line-through; }
.al-new { color: #166534; font-weight: 700; }
.al-arrow { color: #94a3b8; font-size: .6rem; }

.al-module-badge {
    display: inline-block; background: #f1f5f9; color: #475569;
    padding: .25rem .5rem; border-radius: 6px;
    font-size: .7rem; font-weight: 700;
}
.al-module-badge small { color: #94a3b8; }

.al-ip { font-family: 'Courier New', monospace; font-size: .72rem; color: #64748b; }

/* Glass Card tweaks */
.glass-card { background: rgba(255,255,255,.88) !important; border-radius: 20px; border: 1px solid rgba(255,255,255,.3); box-shadow: 0 8px 32px rgba(0,0,0,.04); }
.header-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }

@media (max-width: 768px) {
    .al-description { max-width: 180px; }
    .al-table td, .al-table th { padding: .7rem .8rem; }
}
</style>
@endpush

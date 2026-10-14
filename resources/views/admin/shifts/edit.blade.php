@extends('layouts.admin')

@section('title', 'Edit Work Shift')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Work Shift: {{ $shift->name }}</h2>
            <p class="text-muted small mb-0">Modify configuration details and roster settings.</p>
        </div>
        <a href="{{ route('admin.shifts.index') }}" class="btn btn-outline-secondary fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Please fix the errors below:</h6>
            <ul class="mb-0 px-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.shifts.update', $shift) }}" method="POST" id="shiftForm">
        @csrf
        @method('PUT')

        {{-- 1. SHIFT DETAILS CARD --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i> Shift Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Shift Name*</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. GEN" value="{{ old('name', $shift->name) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Shift Code *(3 Char only)</label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="GEN" value="{{ old('code', $shift->code) }}" maxlength="3" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Start Time*</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">End Time*</label>
                        <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted">Shift Duration</label>
                        <input type="text" id="shift_duration" class="form-control bg-light" value="{{ $shift->shift_duration }}" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fw-bold">Grace (Min)</label>
                        <input type="number" name="grace_period_minutes" class="form-control" value="{{ old('grace_period_minutes', $shift->grace_period_minutes) }}" min="0" max="120">
                    </div>
                    <div class="col-md-2 d-flex align-items-end mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_2hour_permission_enabled" id="is_2hour_permission_enabled" value="1" {{ old('is_2hour_permission_enabled', $shift->is_2hour_permission_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary" for="is_2hour_permission_enabled">
                                Enable 2-Hr Permission
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. SHIFT BREAK DETAIL CARD --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-mug-hot me-2"></i> Shift Break Detail</h5>
            </div>
            <div class="card-body bg-light">
                <div class="row align-items-center g-3">
                    <div class="col-md-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input break-check" type="checkbox" name="break1_enabled" id="break1_enabled" value="1" {{ old('break1_enabled', $shift->break1_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="break1_enabled">Lunch Break</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input break-check" type="checkbox" name="break2_enabled" id="break2_enabled" value="1" {{ old('break2_enabled', $shift->break2_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="break2_enabled">Break 2</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input break-check" type="checkbox" name="break3_enabled" id="break3_enabled" value="1" {{ old('break3_enabled', $shift->break3_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="break3_enabled">Break 3</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input break-check" type="checkbox" name="break4_enabled" id="break4_enabled" value="1" {{ old('break4_enabled', $shift->break4_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="break4_enabled">Break 4</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input break-check" type="checkbox" name="break5_enabled" id="break5_enabled" value="1" {{ old('break5_enabled', $shift->break5_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="break5_enabled">Break 5</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input break-check" type="checkbox" name="break6_enabled" id="break6_enabled" value="1" {{ old('break6_enabled', $shift->break6_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="break6_enabled">Break 6</label>
                        </div>
                        <button type="button" id="btn_set_default_breaks" class="btn btn-outline-primary btn-sm fw-bold mt-2">
                            <i class="fas fa-clock"></i> Set Default Breaks
                        </button>
                    </div>

                    <div class="col-md-7">
                        <div class="row g-2 mb-2 break1-fields">
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Begin Time</label>
                                <input type="time" name="break1_begin_time" id="break1_begin_time" class="form-control form-control-sm break-input" value="{{ old('break1_begin_time', $shift->break1_begin_time ? \Carbon\Carbon::parse($shift->break1_begin_time)->format('H:i') : '13:00') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">End Time</label>
                                <input type="time" name="break1_end_time" id="break1_end_time" class="form-control form-control-sm break-input" value="{{ old('break1_end_time', $shift->break1_end_time ? \Carbon\Carbon::parse($shift->break1_end_time)->format('H:i') : '14:00') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Lunch Break Duration</label>
                                <input type="text" id="break1_duration" class="form-control form-control-sm bg-light" value="{{ $shift->break1_duration ?? '0:00' }}" readonly>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 break2-fields">
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Begin Time</label>
                                <input type="time" name="break2_begin_time" id="break2_begin_time" class="form-control form-control-sm break-input" value="{{ old('break2_begin_time', $shift->break2_begin_time ? \Carbon\Carbon::parse($shift->break2_begin_time)->format('H:i') : '16:00') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">End Time</label>
                                <input type="time" name="break2_end_time" id="break2_end_time" class="form-control form-control-sm break-input" value="{{ old('break2_end_time', $shift->break2_end_time ? \Carbon\Carbon::parse($shift->break2_end_time)->format('H:i') : '16:15') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Break 2 Duration</label>
                                <input type="text" id="break2_duration" class="form-control form-control-sm bg-light" value="{{ $shift->break2_duration ?? '0:00' }}" readonly>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 break3-fields">
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Begin Time</label>
                                <input type="time" name="break3_begin_time" id="break3_begin_time" class="form-control form-control-sm break-input" value="{{ old('break3_begin_time', $shift->break3_begin_time ? \Carbon\Carbon::parse($shift->break3_begin_time)->format('H:i') : '11:00') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">End Time</label>
                                <input type="time" name="break3_end_time" id="break3_end_time" class="form-control form-control-sm break-input" value="{{ old('break3_end_time', $shift->break3_end_time ? \Carbon\Carbon::parse($shift->break3_end_time)->format('H:i') : '11:15') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Break 3 Duration</label>
                                <input type="text" id="break3_duration" class="form-control form-control-sm bg-light" value="{{ $shift->break3_duration ?? '0:00' }}" readonly>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 break4-fields">
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Begin Time</label>
                                <input type="time" name="break4_begin_time" id="break4_begin_time" class="form-control form-control-sm break-input" value="{{ old('break4_begin_time', $shift->break4_begin_time ? \Carbon\Carbon::parse($shift->break4_begin_time)->format('H:i') : '15:00') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">End Time</label>
                                <input type="time" name="break4_end_time" id="break4_end_time" class="form-control form-control-sm break-input" value="{{ old('break4_end_time', $shift->break4_end_time ? \Carbon\Carbon::parse($shift->break4_end_time)->format('H:i') : '15:15') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Break 4 Duration</label>
                                <input type="text" id="break4_duration" class="form-control form-control-sm bg-light" value="{{ $shift->break4_duration ?? '0:00' }}" readonly>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 break5-fields">
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Begin Time</label>
                                <input type="time" name="break5_begin_time" id="break5_begin_time" class="form-control form-control-sm break-input" value="{{ old('break5_begin_time', $shift->break5_begin_time ? \Carbon\Carbon::parse($shift->break5_begin_time)->format('H:i') : '10:00') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">End Time</label>
                                <input type="time" name="break5_end_time" id="break5_end_time" class="form-control form-control-sm break-input" value="{{ old('break5_end_time', $shift->break5_end_time ? \Carbon\Carbon::parse($shift->break5_end_time)->format('H:i') : '10:15') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Break 5 Duration</label>
                                <input type="text" id="break5_duration" class="form-control form-control-sm bg-light" value="{{ $shift->break5_duration ?? '0:00' }}" readonly>
                            </div>
                        </div>
                        <div class="row g-2 break6-fields">
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Begin Time</label>
                                <input type="time" name="break6_begin_time" id="break6_begin_time" class="form-control form-control-sm break-input" value="{{ old('break6_begin_time', $shift->break6_begin_time ? \Carbon\Carbon::parse($shift->break6_begin_time)->format('H:i') : '12:00') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">End Time</label>
                                <input type="time" name="break6_end_time" id="break6_end_time" class="form-control form-control-sm break-input" value="{{ old('break6_end_time', $shift->break6_end_time ? \Carbon\Carbon::parse($shift->break6_end_time)->format('H:i') : '12:15') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1 fw-bold text-muted">Break 6 Duration</label>
                                <input type="text" id="break6_duration" class="form-control form-control-sm bg-light" value="{{ $shift->break6_duration ?? '0:00' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 border-start ps-3">
                        <label class="form-label small fw-bold text-muted">break details</label>
                        <select name="break_details_policy" class="form-select form-select-sm">
                            <option value="1 One In & Last Out" {{ old('break_details_policy', $shift->break_details_policy) == '1 One In & Last Out' ? 'selected' : '' }}>1 One In & Last Out</option>
                            <option value="2 Two In & Last Out" {{ old('break_details_policy', $shift->break_details_policy) == '2 Two In & Last Out' ? 'selected' : '' }}>2 Two In & Last Out</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. SECOND WEEKLY OFF DAYS CONDITION CARD --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2"></i> Second Weekly Off Days Condition</h5>
            </div>
            <div class="card-body">
                <div class="border-bottom pb-3 mb-3">
                    <label class="form-label fw-bold d-block text-primary small">Select Weeks affected by Second Weekly Off Roster:</label>
                    <div class="d-flex gap-4">
                        @foreach(['I', 'II', 'III', 'IV', 'V'] as $week)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="second_weekly_off_weeks[]" value="{{ $week }}" id="week_{{ $week }}" {{ in_array($week, old('second_weekly_off_weeks', $selectedWeeks)) ? 'checked' : '' }}>
                                <label class="form-check-label font-monospace fw-bold" for="week_{{ $week }}">{{ $week }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">First Weekly Off</label>
                        <select name="first_weekly_off" class="form-select">
                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'None'] as $day)
                                <option value="{{ $day }}" {{ old('first_weekly_off', $shift->first_weekly_off) == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Second Weekly Off</label>
                        <select name="second_weekly_off" class="form-select">
                            @foreach(['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'None'] as $day)
                                <option value="{{ $day }}" {{ old('second_weekly_off', $shift->second_weekly_off) == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Second WO Type</label>
                        <select name="second_wo_type" class="form-select">
                            <option value="Full" {{ old('second_wo_type', $shift->second_wo_type) == 'Full' ? 'selected' : '' }}>Full</option>
                            <option value="Half" {{ old('second_wo_type', $shift->second_wo_type) == 'Half' ? 'selected' : '' }}>Half</option>
                            <option value="None" {{ old('second_wo_type', $shift->second_wo_type) == 'None' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Half Day Shift</label>
                        <select name="half_day_shift" class="form-select">
                            @foreach(['None', 'Saturday', 'Sunday', 'Friday'] as $day)
                                <option value="{{ $day }}" {{ old('half_day_shift', $shift->half_day_shift) == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Remove Duplicate Punch (Min)</label>
                        <input type="text" name="remove_duplicate_punch" class="form-control" placeholder="00:01" value="{{ old('remove_duplicate_punch', $shift->remove_duplicate_punch) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Max. Early Hour Allow</label>
                        <input type="text" name="max_early_hour_allow" class="form-control" placeholder="02:00" value="{{ old('max_early_hour_allow', $shift->max_early_hour_allow) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Max OT Allow</label>
                        <input type="text" name="max_ot_allow" class="form-control" placeholder="01:00" value="{{ old('max_ot_allow', $shift->max_ot_allow) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">No. of Present for Weekly Off</label>
                        <input type="text" name="no_of_present_for_weekly_off" class="form-control" placeholder="0.00" value="{{ old('no_of_present_for_weekly_off', $shift->no_of_present_for_weekly_off) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. ABSENT & SINGLE PUNCH POLICY CARD --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-user-slash me-2"></i> Mark as Absent as weekly Off as</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="mark_absent_aaa" id="mark_absent_aaa" value="1" {{ old('mark_absent_aaa', $shift->mark_absent_aaa) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="mark_absent_aaa">AAA</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="mark_absent_l_wo_l" id="mark_absent_l_wo_l" value="1" {{ old('mark_absent_l_wo_l', $shift->mark_absent_l_wo_l) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="mark_absent_l_wo_l">L-WO-L</label>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <label class="form-label fw-bold small mb-0 me-3">Single Punch Policy</label>
                        <select name="single_punch_policy" class="form-select w-50">
                            <option value="None" {{ old('single_punch_policy', $shift->single_punch_policy) == 'None' ? 'selected' : '' }}>None</option>
                            <option value="Present" {{ old('single_punch_policy', $shift->single_punch_policy) == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ old('single_punch_policy', $shift->single_punch_policy) == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Half Day" {{ old('single_punch_policy', $shift->single_punch_policy) == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mb-5">
            <button type="submit" class="btn btn-success btn-lg px-5 fw-bold shadow-sm">
                <i class="fas fa-save me-1"></i> Update Shift Details
            </button>
        </div>
    </form>
</div>

{{-- DURATION CALCULATION SCRIPTS --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const startTimeEl = document.getElementById("start_time");
    const endTimeEl = document.getElementById("end_time");
    const shiftDurationEl = document.getElementById("shift_duration");

    const break1Enabled = document.getElementById("break1_enabled");
    const break1Begin = document.getElementById("break1_begin_time");
    const break1End = document.getElementById("break1_end_time");
    const break1Duration = document.getElementById("break1_duration");

    const break2Enabled = document.getElementById("break2_enabled");
    const break2Begin = document.getElementById("break2_begin_time");
    const break2End = document.getElementById("break2_end_time");
    const break2Duration = document.getElementById("break2_duration");

    const break3Enabled = document.getElementById("break3_enabled");
    const break3Begin = document.getElementById("break3_begin_time");
    const break3End = document.getElementById("break3_end_time");
    const break3Duration = document.getElementById("break3_duration");

    const break4Enabled = document.getElementById("break4_enabled");
    const break4Begin = document.getElementById("break4_begin_time");
    const break4End = document.getElementById("break4_end_time");
    const break4Duration = document.getElementById("break4_duration");

    const break5Enabled = document.getElementById("break5_enabled");
    const break5Begin = document.getElementById("break5_begin_time");
    const break5End = document.getElementById("break5_end_time");
    const break5Duration = document.getElementById("break5_duration");

    const break6Enabled = document.getElementById("break6_enabled");
    const break6Begin = document.getElementById("break6_begin_time");
    const break6End = document.getElementById("break6_end_time");
    const break6Duration = document.getElementById("break6_duration");

    function calcTimeDiff(startVal, endVal) {
        if (!startVal || !endVal) return "0:00";
        const [sh, sm] = startVal.split(':').map(Number);
        const [eh, em] = endVal.split(':').map(Number);
        let sMins = sh * 60 + sm;
        let eMins = eh * 60 + em;
        if (eMins < sMins) {
            eMins += 24 * 60;
        }
        const diff = eMins - sMins;
        const h = Math.floor(diff / 60);
        const m = diff % 60;
        return `${h}:${m < 10 ? '0' : ''}${m}`;
    }

    function updateMainDuration() {
        shiftDurationEl.value = calcTimeDiff(startTimeEl.value, endTimeEl.value);
    }

    function updateBreak1Duration() {
        if (break1Enabled.checked) {
            break1Duration.value = calcTimeDiff(break1Begin.value, break1End.value);
        } else {
            break1Duration.value = "0:00";
        }
    }

    function updateBreak2Duration() {
        if (break2Enabled.checked) {
            break2Duration.value = calcTimeDiff(break2Begin.value, break2End.value);
        } else {
            break2Duration.value = "0:00";
        }
    }

    function updateBreak3Duration() {
        if (break3Enabled.checked) {
            break3Duration.value = calcTimeDiff(break3Begin.value, break3End.value);
        } else {
            break3Duration.value = "0:00";
        }
    }

    function updateBreak4Duration() {
        if (break4Enabled.checked) {
            break4Duration.value = calcTimeDiff(break4Begin.value, break4End.value);
        } else {
            break4Duration.value = "0:00";
        }
    }

    function updateBreak5Duration() {
        if (break5Enabled.checked) {
            break5Duration.value = calcTimeDiff(break5Begin.value, break5End.value);
        } else {
            break5Duration.value = "0:00";
        }
    }

    function updateBreak6Duration() {
        if (break6Enabled.checked) {
            break6Duration.value = calcTimeDiff(break6Begin.value, break6End.value);
        } else {
            break6Duration.value = "0:00";
        }
    }

    // Toggle disabled state
    function toggleBreakInputs() {
        break1Begin.disabled = !break1Enabled.checked;
        break1End.disabled = !break1Enabled.checked;
        updateBreak1Duration();

        break2Begin.disabled = !break2Enabled.checked;
        break2End.disabled = !break2Enabled.checked;
        updateBreak2Duration();

        break3Begin.disabled = !break3Enabled.checked;
        break3End.disabled = !break3Enabled.checked;
        updateBreak3Duration();

        break4Begin.disabled = !break4Enabled.checked;
        break4End.disabled = !break4Enabled.checked;
        updateBreak4Duration();

        break5Begin.disabled = !break5Enabled.checked;
        break5End.disabled = !break5Enabled.checked;
        updateBreak5Duration();

        break6Begin.disabled = !break6Enabled.checked;
        break6End.disabled = !break6Enabled.checked;
        updateBreak6Duration();
    }

    // Listeners
    startTimeEl.addEventListener("input", updateMainDuration);
    endTimeEl.addEventListener("input", updateMainDuration);

    break1Enabled.addEventListener("change", toggleBreakInputs);
    break1Begin.addEventListener("input", updateBreak1Duration);
    break1End.addEventListener("input", updateBreak1Duration);

    break2Enabled.addEventListener("change", toggleBreakInputs);
    break2Begin.addEventListener("input", updateBreak2Duration);
    break2End.addEventListener("input", updateBreak2Duration);

    break3Enabled.addEventListener("change", toggleBreakInputs);
    break3Begin.addEventListener("input", updateBreak3Duration);
    break3End.addEventListener("input", updateBreak3Duration);

    break4Enabled.addEventListener("change", toggleBreakInputs);
    break4Begin.addEventListener("input", updateBreak4Duration);
    break4End.addEventListener("input", updateBreak4Duration);

    break5Enabled.addEventListener("change", toggleBreakInputs);
    break5Begin.addEventListener("input", updateBreak5Duration);
    break5End.addEventListener("input", updateBreak5Duration);

    break6Enabled.addEventListener("change", toggleBreakInputs);
    break6Begin.addEventListener("input", updateBreak6Duration);
    break6End.addEventListener("input", updateBreak6Duration);

    const btnSetDefault = document.getElementById("btn_set_default_breaks");
    btnSetDefault.addEventListener("click", function() {
        break1Enabled.checked = true;
        break2Enabled.checked = true;
        break3Enabled.checked = false;
        break4Enabled.checked = false;
        break5Enabled.checked = false;
        break6Enabled.checked = false;
        break1Begin.value = "13:00";
        break1End.value = "14:00";
        break2Begin.value = "16:00";
        break2End.value = "16:15";
        toggleBreakInputs();
    });

    // Initial run
    toggleBreakInputs();
    updateMainDuration();
});
</script>
@endsection

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('reminders:shift-end')->everyMinute();

Artisan::command('attendance:auto-mark-absent', function () {
    \App\Models\Attendance::autoMarkMissedCheckouts();
    $this->info('Missed checkouts auto-marked as absent successfully.');
})->purpose('Auto mark missed checkouts as absent if shift ended');

Schedule::command('attendance:auto-mark-absent')->hourly();

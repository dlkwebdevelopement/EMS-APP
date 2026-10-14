<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$att = \App\Models\Attendance::first();
echo "Check In: " . $att->check_in . "\n";
echo "Check Out: " . $att->check_out . "\n";

$checkIn = \Carbon\Carbon::parse($att->check_in);
$checkOut = $att->check_out ? \Carbon\Carbon::parse($att->check_out) : \Carbon\Carbon::now();
        
$totalSeconds = $checkOut->diffInSeconds($checkIn);
echo "Total Secs: " . $totalSeconds . "\n";

$totalBreakSecs = ($att->break_duration_seconds ?? 0) + ($att->lunch_duration_seconds ?? 0);
if ($totalBreakSecs <= 0 && $att->total_break_minutes > 0) {
    $totalBreakSecs = $att->total_break_minutes * 60;
}

$workingSeconds = $totalSeconds - $totalBreakSecs;
echo "Working Secs: " . $workingSeconds . "\n";

echo "Formatted: " . $att->formatted_working_hours . "\n";


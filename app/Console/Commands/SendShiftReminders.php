<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkShift;
use App\Models\Employee;
use App\Services\PushService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendShiftReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:shift-end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notification reminders 10 and 5 minutes before shift end time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        // Fetch all work shifts
        $shifts = WorkShift::all();
        $notifiedCount = 0;

        foreach ($shifts as $shift) {
            if (!$shift->end_time) {
                continue;
            }

            $endTime = Carbon::createFromFormat('H:i:s', $shift->end_time);
            
            // Check exactly 10 minutes before
            $tenMinBefore = (clone $endTime)->subMinutes(10)->format('H:i');
            
            // Check exactly 5 minutes before
            $fiveMinBefore = (clone $endTime)->subMinutes(5)->format('H:i');

            if ($currentTime === $tenMinBefore || $currentTime === $fiveMinBefore) {
                $minutesRemaining = ($currentTime === $tenMinBefore) ? 10 : 5;
                
                // Get all active employees assigned to this shift with a push token
                $employees = Employee::where('shift_id', $shift->id)
                    ->where('is_active', true)
                    ->whereNotNull('push_token')
                    ->get();

                $tokens = $employees->pluck('push_token')->toArray();

                if (!empty($tokens)) {
                    $title = "Shift Ending Soon ⏰";
                    $body = "Your shift ends in {$minutesRemaining} minutes. Please ensure your timesheet is updated!";
                    
                    PushService::send($tokens, $title, $body, [
                        'type' => 'shift_reminder',
                        'minutes_remaining' => $minutesRemaining
                    ]);
                    
                    $notifiedCount += count($tokens);
                }
            }
        }

        $this->info("Checked shift reminders. Sent {$notifiedCount} notifications.");
        Log::info("Shift reminders checked. Sent {$notifiedCount} notifications.");
    }
}

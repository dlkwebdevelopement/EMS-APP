<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    /**
     * Display upcoming company holidays.
     */
    public function holidays()
    {
        try {
            if (Schema::hasTable('holidays')) {
                $holidays = Holiday::where('date', '>=', now()->startOfDay())
                    ->orderBy('date', 'asc')
                    ->limit(20)
                    ->get();

                if ($holidays->count() > 0) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $holidays
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log fallback
        }

        // Standard Default Public Holidays
        return response()->json([
            'status' => 'success',
            'data' => [
                ['id' => 1, 'name' => 'Independence Day', 'date' => '2026-08-15', 'description' => 'National Holiday'],
                ['id' => 2, 'name' => 'Gandhi Jayanti', 'date' => '2026-10-02', 'description' => 'National Holiday'],
                ['id' => 3, 'name' => 'Diwali / Deepavali', 'date' => '2026-11-08', 'description' => 'Festival Holiday'],
                ['id' => 4, 'name' => 'Christmas', 'date' => '2026-12-25', 'description' => 'Public Holiday']
            ]
        ]);
    }

    /**
     * Display upcoming peer birthdays.
     */
    public function birthdays()
    {
        try {
            $employees = Employee::where('is_active', true)
                ->whereNotNull('date_of_birth')
                ->get();

            $upcoming = $employees->filter(function($emp) {
                $dob = Carbon::parse($emp->date_of_birth);
                $birthdayThisYear = $dob->copy()->year(now()->year);
                
                if ($birthdayThisYear->isPast()) {
                    $birthdayThisYear->addYear();
                }
                
                return $birthdayThisYear->diffInDays(now()) <= 60;
            })->values();

            return response()->json([
                'status' => 'success',
                'data' => $upcoming->map(function($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->name,
                        'department' => $emp->department,
                        'date' => Carbon::parse($emp->date_of_birth)->format('M d'),
                        'type' => 'birthday'
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }
    }
}

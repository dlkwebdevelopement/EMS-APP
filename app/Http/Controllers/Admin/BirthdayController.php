<?php
// app/Http/Controllers/Admin/BirthdayController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\BirthdayNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BirthdayController extends Controller
{
    public function index()
    {
        $today = now();
        
        // Today's birthdays
        $todayBirthdays = Employee::whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth', $today->day)
            ->where('is_active', true)
            ->get();
            
        // Today's anniversaries
        $todayAnniversaries = Employee::whereMonth('anniversary_date', $today->month)
            ->whereDay('anniversary_date', $today->day)
            ->where('is_active', true)
            ->get();
            
        // Upcoming birthdays (next 7 days)
        $upcomingBirthdays = [];
        for ($i = 1; $i <= 7; $i++) {
            $date = $today->copy()->addDays($i);
            $birthdays = Employee::whereMonth('date_of_birth', $date->month)
                ->whereDay('date_of_birth', $date->day)
                ->where('is_active', true)
                ->get();
            if ($birthdays->count() > 0) {
                $upcomingBirthdays[] = [
                    'date' => $date,
                    'employees' => $birthdays
                ];
            }
        }
        
        // Upcoming anniversaries
        $upcomingAnniversaries = [];
        for ($i = 1; $i <= 7; $i++) {
            $date = $today->copy()->addDays($i);
            $anniversaries = Employee::whereMonth('anniversary_date', $date->month)
                ->whereDay('anniversary_date', $date->day)
                ->where('is_active', true)
                ->get();
            if ($anniversaries->count() > 0) {
                $upcomingAnniversaries[] = [
                    'date' => $date,
                    'employees' => $anniversaries
                ];
            }
        }
        
        // Birthday notifications history
        $notifications = BirthdayNotification::with('employee')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
            
        return view('admin.birthdays.index', compact(
            'todayBirthdays', 
            'todayAnniversaries', 
            'upcomingBirthdays', 
            'upcomingAnniversaries',
            'notifications'
        ));
    }
    
    public function sendWish(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:birthday,anniversary',
            'message' => 'required|string|max:500'
        ]);
        
        $employee = Employee::find($request->employee_id);
        
        // Send email wish
        Mail::send('emails.birthday-wish', [
            'employee' => $employee,
            'type' => $request->type,
            'message' => $request->message
        ], function($message) use ($employee) {
            $message->to($employee->email)
                    ->subject("Happy " . ucfirst($request->type) . " from the Team!");
        });
        
        // Create notification record
        BirthdayNotification::create([
            'employee_id' => $employee->id,
            'type' => $request->type,
            'date' => now(),
            'year' => now()->year,
            'is_sent' => true,
            'sent_at' => now(),
            'message' => $request->message
        ]);
        
        // Create in-app notification
        $employee->notifications()->create([
            'title' => "Happy " . ucfirst($request->type) . "!",
            'message' => $request->message,
            'type' => 'birthday'
        ]);
        
        return redirect()->back()->with('success', ucfirst($request->type) . ' wish sent successfully!');
    }
    
    public function bulkSendWishes(Request $request)
    {
        $request->validate([
            'type' => 'required|in:birthday,anniversary',
            'message' => 'required|string|max:500'
        ]);
        
        $today = now();
        $employees = [];
        
        if ($request->type == 'birthday') {
            $employees = Employee::whereMonth('date_of_birth', $today->month)
                ->whereDay('date_of_birth', $today->day)
                ->where('is_active', true)
                ->get();
        } else {
            $employees = Employee::whereMonth('anniversary_date', $today->month)
                ->whereDay('anniversary_date', $today->day)
                ->where('is_active', true)
                ->get();
        }
        
        $sent = 0;
        foreach ($employees as $employee) {
            // Send email wish
            Mail::send('emails.birthday-wish', [
                'employee' => $employee,
                'type' => $request->type,
                'message' => $request->message
            ], function($message) use ($employee) {
                $message->to($employee->email)
                        ->subject("Happy " . ucfirst($request->type) . " from the Team!");
            });
            
            // Create notification record
            BirthdayNotification::create([
                'employee_id' => $employee->id,
                'type' => $request->type,
                'date' => now(),
                'year' => now()->year,
                'is_sent' => true,
                'sent_at' => now(),
                'message' => $request->message
            ]);
            
            $sent++;
        }
        
        return redirect()->back()->with('success', "{$sent} " . ucfirst($request->type) . " wishes sent successfully!");
    }
    
    public function getUpcoming()
    {
        $birthdays = Employee::whereNotNull('date_of_birth')
            ->where('is_active', true)
            ->get()
            ->map(function($employee) {
                $birthday = Carbon::parse($employee->date_of_birth);
                $nextBirthday = Carbon::createFromDate(now()->year, $birthday->month, $birthday->day);
                if ($nextBirthday->lt(now())) {
                    $nextBirthday->addYear();
                }
                $daysUntil = now()->diffInDays($nextBirthday, false);
                return [
                    'employee' => $employee,
                    'days' => $daysUntil,
                    'date' => $nextBirthday,
                    'type' => 'birthday'
                ];
            })
            ->filter(function($item) {
                return $item['days'] >= 0 && $item['days'] <= 30;
            })
            ->sortBy('days')
            ->values()
            ->take(10);
            
        $anniversaries = Employee::whereNotNull('anniversary_date')
            ->where('is_active', true)
            ->get()
            ->map(function($employee) {
                $anniversary = Carbon::parse($employee->anniversary_date);
                $nextAnniversary = Carbon::createFromDate(now()->year, $anniversary->month, $anniversary->day);
                if ($nextAnniversary->lt(now())) {
                    $nextAnniversary->addYear();
                }
                $daysUntil = now()->diffInDays($nextAnniversary, false);
                return [
                    'employee' => $employee,
                    'days' => $daysUntil,
                    'date' => $nextAnniversary,
                    'type' => 'anniversary'
                ];
            })
            ->filter(function($item) {
                return $item['days'] >= 0 && $item['days'] <= 30;
            })
            ->sortBy('days')
            ->values()
            ->take(10);
            
        return response()->json([
            'birthdays' => $birthdays,
            'anniversaries' => $anniversaries
        ]);
    }
    
    public function updateBirthday(Request $request, Employee $employee)
    {
        $request->validate([
            'date_of_birth' => 'nullable|date',
            'anniversary_date' => 'nullable|date',
            'blood_group' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'hobbies' => 'nullable|string|max:255',
            'favorite_quote' => 'nullable|string|max:500',
            'social_media' => 'nullable|string|max:255'
        ]);
        
        $employee->update($request->only([
            'date_of_birth', 
            'anniversary_date', 
            'blood_group', 
            'hobbies', 
            'favorite_quote', 
            'social_media'
        ]));
        
        return redirect()->back()->with('success', 'Birthday information updated successfully.');
    }
}
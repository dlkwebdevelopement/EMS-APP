<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Delete existing admins if any
        Admin::whereIn('email', ['admin@dlktechnologies.net', 'hr@dlktechnologies.net'])->delete();
        
        // Create admin user with your credentials
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@dlktechnologies.net',  // Changed to your email
            'password' => Hash::make('dlk@1234'),    // Your password
            'is_super_admin' => true,
        ]);
        
        // Optional: Create HR manager
        Admin::create([
            'name' => 'HR Manager',
            'email' => 'hr@dlktechnologies.net',    // Optional
            'password' => Hash::make('dlk@1234'),
            'is_super_admin' => false,
        ]);
        
        $this->command->info('Admin users created successfully!');
        $this->command->info('Email: admin@dlktechnologies.net');
        $this->command->info('Password: dlk@1234');

        // Create Demo Employee
        $demoEmployee = \App\Models\Employee::updateOrCreate(
            ['email' => 'demo@dlktechnologies.net'],
            [
                'employee_code' => 'DLK26@001',
                'name' => 'Demo Employee',
                'password' => \Illuminate\Support\Facades\Hash::make('dlk@1234'),
                'department' => 'Development',
                'designation' => 'Software Engineer',
                'basic_salary' => 35000.00,
                'joining_date' => now(),
                'is_active' => true,
            ]
        );

        $this->command->info('Demo employee created successfully!');

        // Create leave balance for demo employee
        \App\Models\LeaveBalance::updateOrCreate(
            ['employee_id' => $demoEmployee->id, 'year' => date('Y')],
            [
                'casual_leave_total' => 12,
                'sick_leave_total' => 10,
                'annual_leave_total' => 15,
                'casual_leave_used' => 0,
                'sick_leave_used' => 0,
                'annual_leave_used' => 0,
            ]
        );
        $this->command->info('Leave balance created for demo employee!');
        $this->command->info('Email: demo@dlktechnologies.net');
        $this->command->info('Password: dlk@1234');
    }
}
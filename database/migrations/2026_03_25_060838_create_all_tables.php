<?php
// database/migrations/2026_03_25_000000_create_all_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Admins Table
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->boolean('is_super_admin')->default(false);
                $table->timestamp('last_login_at')->nullable();
                $table->string('last_login_ip')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Employees Table
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->string('employee_code')->unique();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->date('anniversary_date')->nullable();
                $table->string('blood_group', 5)->nullable();
                $table->string('hobbies')->nullable();
                $table->text('favorite_quote')->nullable();
                $table->string('social_media')->nullable();
                $table->date('joining_date');
                $table->string('department')->nullable();
                $table->string('designation')->nullable();
                $table->decimal('basic_salary', 10, 2);
                $table->decimal('hourly_rate', 8, 2)->nullable();
                $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
                $table->string('bank_name')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->string('ifsc_code')->nullable();
                $table->string('pan_number')->nullable();
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_phone')->nullable();
                $table->string('emergency_relationship')->nullable();
                $table->string('emergency_alternate_phone')->nullable();
                $table->text('emergency_address')->nullable();
                $table->string('office_ssid')->default('office_wifi');
                $table->string('profile_photo')->nullable();
                $table->string('device_token')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 3. Attendance Table
        if (!Schema::hasTable('attendance')) {
            Schema::create('attendance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->date('date');
                $table->timestamp('check_in')->nullable();
                $table->timestamp('check_out')->nullable();
                $table->enum('status', ['present', 'absent', 'late', 'half_day', 'holiday'])->default('absent');
                $table->decimal('overtime_hours', 5, 2)->default(0);
                $table->enum('check_in_method', ['wifi', 'manual', 'qr'])->default('wifi');
                $table->enum('check_out_method', ['wifi', 'manual', 'qr'])->nullable();
                $table->string('check_in_location')->nullable();
                $table->string('check_out_location')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
                $table->unique(['employee_id', 'date']);
            });
        }

        // 4. Leaves Table
        if (!Schema::hasTable('leaves')) {
            Schema::create('leaves', function (Blueprint $table) {
                $table->id();
                // employee_id must NOT be nullable — every leave belongs to an employee
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->enum('leave_type', ['sick', 'casual', 'annual', 'unpaid', 'maternity', 'paternity']);
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('total_days');
                $table->text('reason');
                $table->string('attachment')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
                // FIX: approved_by references admins, not employees
                // An admin approves/rejects leaves, not an employee
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->foreign('approved_by')->references('id')->on('admins')->onDelete('set null');
                $table->text('rejection_reason')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
            });
        }

        // 5. Leave Balances Table
        if (!Schema::hasTable('leave_balances')) {
            Schema::create('leave_balances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->integer('year');
                $table->integer('sick_leave_total')->default(12);
                $table->integer('sick_leave_used')->default(0);
                $table->integer('casual_leave_total')->default(12);
                $table->integer('casual_leave_used')->default(0);
                $table->integer('annual_leave_total')->default(15);
                $table->integer('annual_leave_used')->default(0);
                $table->integer('unpaid_leave_total')->default(0);
                $table->integer('unpaid_leave_used')->default(0);
                $table->timestamps();
                $table->unique(['employee_id', 'year']);
            });
        }

        // 6. Payrolls Table
        if (!Schema::hasTable('payrolls')) {
            Schema::create('payrolls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->integer('month');
                $table->integer('year');
                $table->decimal('basic_salary', 10, 2);
                $table->integer('present_days')->default(0);
                $table->integer('absent_days')->default(0);
                $table->integer('leave_days')->default(0);
                $table->integer('paid_leave_days')->default(0);
                $table->decimal('overtime_hours', 5, 2)->default(0);
                $table->decimal('overtime_amount', 10, 2)->default(0);
                $table->decimal('allowances', 10, 2)->default(0);
                $table->json('allowances_breakdown')->nullable();
                $table->decimal('deductions', 10, 2)->default(0);
                $table->json('deductions_breakdown')->nullable();
                $table->decimal('gross_salary', 10, 2);
                $table->decimal('net_salary', 10, 2);
                $table->enum('status', ['draft', 'processed', 'paid', 'cancelled'])->default('draft');
                $table->timestamp('processed_date')->nullable();
                $table->timestamp('paid_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['employee_id', 'month', 'year']);
            });
        }

        // 7. Payslips Table
        if (!Schema::hasTable('payslips')) {
            Schema::create('payslips', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->foreignId('payroll_id')->constrained('payrolls')->onDelete('cascade');
                $table->string('file_path');
                $table->string('file_name');
                $table->decimal('amount', 10, 2);
                $table->timestamp('generated_date');
                $table->boolean('is_downloaded')->default(false);
                $table->timestamp('downloaded_at')->nullable();
                $table->boolean('is_emailed')->default(false);
                $table->timestamp('emailed_at')->nullable();
                $table->timestamps();
            });
        }

        // 8. Time Sheets Table
        if (!Schema::hasTable('time_sheets')) {
            Schema::create('time_sheets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->date('date');
                $table->time('start_time');
                $table->time('end_time');
                $table->decimal('break_hours', 4, 2)->default(0);
                $table->decimal('total_hours', 5, 2);
                $table->string('project')->nullable();
                $table->text('task_description');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('remarks')->nullable();
                // FIX: approved_by references admins, not employees
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->foreign('approved_by')->references('id')->on('admins')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                $table->index(['employee_id', 'date']);
            });
        }

        // 9. Birthday Notifications Table
        if (!Schema::hasTable('birthday_notifications')) {
            Schema::create('birthday_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->enum('type', ['birthday', 'anniversary']);
                $table->date('date');
                $table->integer('year');
                $table->boolean('is_sent')->default(false);
                $table->timestamp('sent_at')->nullable();
                $table->text('message')->nullable();
                $table->timestamps();
                $table->index(['date', 'type']);
            });
        }

        // 10. Notifications Table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->string('title');
                $table->text('message');
                $table->enum('type', ['attendance', 'leave', 'payroll', 'general', 'alert', 'birthday', 'anniversary'])->default('general');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }

        // 11. Attendance Logs Table
        if (!Schema::hasTable('attendance_logs')) {
            Schema::create('attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->string('ssid')->nullable();
                $table->enum('action', ['check_in', 'check_out']);
                $table->enum('method', ['wifi', 'manual', 'qr']);
                $table->string('location')->nullable();
                $table->text('device_info')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 12. Departments Table
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->foreignId('manager_id')->nullable()->constrained('employees');
                $table->timestamps();
            });
        }

        // 13. Holidays Table
        if (!Schema::hasTable('holidays')) {
            Schema::create('holidays', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('date')->unique();
                $table->enum('type', ['public', 'company', 'optional'])->default('public');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Laravel Cache Tables
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('birthday_notifications');
        Schema::dropIfExists('time_sheets');
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('admins');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('anniversary_date');
            }
            if (!Schema::hasColumn('employees', 'marital_status')) {
                $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('gender');
            }
            if (!Schema::hasColumn('employees', 'personal_email')) {
                $table->string('personal_email')->nullable()->after('email');
            }
            if (!Schema::hasColumn('employees', 'permanent_address')) {
                $table->text('permanent_address')->nullable()->after('address');
            }
            if (!Schema::hasColumn('employees', 'aadhaar_number')) {
                $table->string('aadhaar_number')->nullable()->after('pan_number');
            }
            if (!Schema::hasColumn('employees', 'uan_number')) {
                $table->string('uan_number')->nullable()->after('aadhaar_number');
            }
            if (!Schema::hasColumn('employees', 'pf_number')) {
                $table->string('pf_number')->nullable()->after('uan_number');
            }
            if (!Schema::hasColumn('employees', 'reporting_manager_id')) {
                $table->foreignId('reporting_manager_id')->nullable()->after('designation')->constrained('employees')->onDelete('set null');
            }
            if (!Schema::hasColumn('employees', 'employment_status')) {
                $table->enum('employment_status', ['probation', 'active', 'notice_period', 'resigned', 'terminated'])->default('active')->after('employment_type');
            }
            if (!Schema::hasColumn('employees', 'probation_end_date')) {
                $table->date('probation_end_date')->nullable()->after('joining_date');
            }
            if (!Schema::hasColumn('employees', 'resignation_date')) {
                $table->date('resignation_date')->nullable()->after('probation_end_date');
            }
            if (!Schema::hasColumn('employees', 'last_working_day')) {
                $table->date('last_working_day')->nullable()->after('resignation_date');
            }
            if (!Schema::hasColumn('employees', 'termination_date')) {
                $table->date('termination_date')->nullable()->after('last_working_day');
            }
            if (!Schema::hasColumn('employees', 'termination_reason')) {
                $table->text('termination_reason')->nullable()->after('termination_date');
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['reporting_manager_id']);
            $table->dropColumn([
                'gender',
                'marital_status',
                'personal_email',
                'permanent_address',
                'aadhaar_number',
                'uan_number',
                'pf_number',
                'reporting_manager_id',
                'employment_status',
                'probation_end_date',
                'resignation_date',
                'last_working_day',
                'termination_date',
                'termination_reason'
            ]);
        });
    }
};

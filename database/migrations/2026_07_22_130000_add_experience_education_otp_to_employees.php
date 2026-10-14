<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'is_fresher')) {
                $table->boolean('is_fresher')->default(true)->after('employment_type');
            }
            if (!Schema::hasColumn('employees', 'total_experience_years')) {
                $table->integer('total_experience_years')->default(0)->after('is_fresher');
            }
            if (!Schema::hasColumn('employees', 'total_experience_months')) {
                $table->integer('total_experience_months')->default(0)->after('total_experience_years');
            }
            if (!Schema::hasColumn('employees', 'previous_organization')) {
                $table->string('previous_organization')->nullable()->after('total_experience_months');
            }
            if (!Schema::hasColumn('employees', 'previous_designation')) {
                $table->string('previous_designation')->nullable()->after('previous_organization');
            }
            if (!Schema::hasColumn('employees', 'internship_details')) {
                $table->text('internship_details')->nullable()->after('previous_designation');
            }
            if (!Schema::hasColumn('employees', 'education_qualification')) {
                $table->string('education_qualification')->nullable()->after('internship_details');
            }
            if (!Schema::hasColumn('employees', 'education_institution')) {
                $table->string('education_institution')->nullable()->after('education_qualification');
            }
            if (!Schema::hasColumn('employees', 'education_passout_year')) {
                $table->string('education_passout_year')->nullable()->after('education_institution');
            }
            if (!Schema::hasColumn('employees', 'education_score')) {
                $table->string('education_score')->nullable()->after('education_passout_year');
            }
            if (!Schema::hasColumn('employees', 'password_reset_otp')) {
                $table->string('password_reset_otp')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('employees', 'password_reset_otp_expires_at')) {
                $table->timestamp('password_reset_otp_expires_at')->nullable()->after('password_reset_otp');
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'is_fresher',
                'total_experience_years',
                'total_experience_months',
                'previous_organization',
                'previous_designation',
                'internship_details',
                'education_qualification',
                'education_institution',
                'education_passout_year',
                'education_score',
                'password_reset_otp',
                'password_reset_otp_expires_at',
            ]);
        });
    }
};

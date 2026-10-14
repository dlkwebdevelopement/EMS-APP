<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('company_name');
            $table->string('designation');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current_job')->default(false);
            $table->text('job_description')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('level')->default('Undergraduate'); // e.g. 10th, 12th, Diploma, Undergraduate, Postgraduate, Doctorate
            $table->string('degree_title'); // e.g. B.Tech Computer Science
            $table->string('institution_name'); // e.g. Anna University
            $table->string('board_or_university')->nullable();
            $table->string('passout_year')->nullable(); // e.g. 2024
            $table->string('score_or_grade')->nullable(); // e.g. 8.5 CGPA / 85%
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_educations');
        Schema::dropIfExists('employee_experiences');
    }
};

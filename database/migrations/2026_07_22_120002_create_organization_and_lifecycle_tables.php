<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('code')->nullable()->unique();
                $table->text('description')->nullable();
                $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('designations')) {
            Schema::create('designations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_lifecycle_histories')) {
            Schema::create('employee_lifecycle_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->string('previous_status')->nullable();
                $table->string('new_status');
                $table->date('change_date');
                $table->text('remarks')->nullable();
                $table->foreignId('changed_by_admin_id')->nullable()->constrained('admins')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('employee_lifecycle_histories');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
    }
};

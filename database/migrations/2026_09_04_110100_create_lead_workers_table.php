<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lead_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('assigned_by_tl_id')->nullable()->constrained('employees')->nullOnDelete();
            
            // Project execution timeline
            $table->date('start_date');
            $table->date('end_date'); // Target completion deadline
            
            // Execution Status
            $table->enum('status', ['assigned', 'in_progress', 'submitted', 'completed', 'overdue'])
                  ->default('assigned')
                  ->index();
                  
            // Task notes & instructions
            $table->text('instructions')->nullable();
            
            // Worker submission
            $table->text('submission_notes')->nullable();
            $table->dateTime('submitted_at')->nullable();
            
            $table->timestamps();

            // Indexes for query performance
            $table->index(['lead_id', 'employee_id']);
            $table->index(['employee_id', 'status']);
            $table->index(['end_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_workers');
    }
};

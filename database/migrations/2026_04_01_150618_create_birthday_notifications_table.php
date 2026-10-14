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
        if (!Schema::hasTable('birthday_notifications')) {
            Schema::create('birthday_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                $table->enum('type', ['birthday', 'anniversary']);
                $table->date('date');
                $table->integer('year');
                $table->boolean('is_sent')->default(true);
                $table->timestamp('sent_at')->nullable();
                $table->text('message')->nullable();
                $table->timestamps();

                // Indexes for better performance
                $table->index(['year', 'type']);
                $table->index('sent_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthday_notifications');
    }
};
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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('program_tech_stack')->nullable();
            $table->text('full_details')->nullable();
            $table->text('frontend_section')->nullable();
            $table->text('backend_section')->nullable();
            $table->text('database')->nullable();
            $table->text('server_host_details')->nullable();
            $table->string('site_link')->nullable();
            $table->text('credentials_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

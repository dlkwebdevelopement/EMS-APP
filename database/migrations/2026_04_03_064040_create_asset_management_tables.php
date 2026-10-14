<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Asset Categories
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Assets
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('asset_tag')->unique(); // Laptop Number / Unique Tag
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->unique();
            $table->json('specs')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->decimal('purchase_cost', 12, 2)->default(0);
            $table->enum('status', ['available', 'allocated', 'faulty', 'repairing', 'retired'])->default('available');
            $table->string('profile_photo')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('asset_categories')->onDelete('cascade');
        });

        // 3. Asset Allocations
        Schema::create('asset_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamp('allocated_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();
            $table->string('condition_on_allocation')->default('Good');
            $table->string('condition_on_return')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        // 4. Asset Maintenances
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->text('issue_description');
            $table->text('action_taken')->nullable();
            $table->string('service_provider')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->timestamp('maintenance_at')->useCurrent();
            $table->timestamp('completion_at')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->boolean('was_replaced')->default(false);
            $table->unsignedBigInteger('replacement_asset_id')->nullable(); // Link to the new asset if replaced
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('replacement_asset_id')->references('id')->on('assets')->onDelete('set null');
        });

        // 5. Audit Logs (Admin Management Activity)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'admin' or 'employee'
            $table->unsignedBigInteger('user_id');
            $table->string('action'); // 'create', 'update', 'delete', 'allocation', 'maintenance'
            $table->string('description');
            $table->string('model_type')->nullable(); // e.g., 'App\Models\Asset'
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('asset_allocations');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
};

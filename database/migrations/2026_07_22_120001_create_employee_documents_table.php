<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employee_documents')) {
            Schema::create('employee_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->string('document_type');
                $table->string('title');
                $table->string('document_number')->nullable();
                $table->string('file_path');
                $table->string('file_name');
                $table->integer('file_size')->nullable();
                $table->string('mime_type')->nullable();
                $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('admins')->onDelete('set null');
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('employee_documents');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->boolean('break3_enabled')->default(false)->after('break2_duration');
            $table->time('break3_begin_time')->nullable()->after('break3_enabled');
            $table->time('break3_end_time')->nullable()->after('break3_begin_time');
            $table->string('break3_duration')->nullable()->after('break3_end_time');

            $table->boolean('break4_enabled')->default(false)->after('break3_duration');
            $table->time('break4_begin_time')->nullable()->after('break4_enabled');
            $table->time('break4_end_time')->nullable()->after('break4_begin_time');
            $table->string('break4_duration')->nullable()->after('break4_end_time');

            $table->boolean('break5_enabled')->default(false)->after('break4_duration');
            $table->time('break5_begin_time')->nullable()->after('break5_enabled');
            $table->time('break5_end_time')->nullable()->after('break5_begin_time');
            $table->string('break5_duration')->nullable()->after('break5_end_time');

            $table->boolean('break6_enabled')->default(false)->after('break5_duration');
            $table->time('break6_begin_time')->nullable()->after('break6_enabled');
            $table->time('break6_end_time')->nullable()->after('break6_begin_time');
            $table->string('break6_duration')->nullable()->after('break6_end_time');
        });
    }

    public function down(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->dropColumn([
                'break3_enabled', 'break3_begin_time', 'break3_end_time', 'break3_duration',
                'break4_enabled', 'break4_begin_time', 'break4_end_time', 'break4_duration',
                'break5_enabled', 'break5_begin_time', 'break5_end_time', 'break5_duration',
                'break6_enabled', 'break6_begin_time', 'break6_end_time', 'break6_duration'
            ]);
        });
    }
};

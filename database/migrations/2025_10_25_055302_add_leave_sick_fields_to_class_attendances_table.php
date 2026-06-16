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
        Schema::table('class_attendances', function (Blueprint $table) {
            $table->boolean('is_leave')->default(false)->after('status');
            $table->boolean('is_sick')->default(false)->after('is_leave');
            $table->text('leave_reason')->nullable()->after('is_sick');
            $table->text('sick_reason')->nullable()->after('leave_reason');
            $table->date('leave_start_date')->nullable()->after('sick_reason');
            $table->date('leave_end_date')->nullable()->after('leave_start_date');
            $table->enum('leave_type', ['personal', 'family', 'emergency', 'other'])->nullable()->after('leave_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            $table->dropColumn([
                'is_leave',
                'is_sick',
                'leave_reason',
                'sick_reason',
                'leave_start_date',
                'leave_end_date',
                'leave_type'
            ]);
        });
    }
};

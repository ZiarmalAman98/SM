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
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->date('start_date'); // Start date of leave
            $table->date('end_date');   // End date of leave
            $table->string('leave_type', 50)->nullable();
            $table->boolean('status')->default(null); // Approved (true) or Pending (false)
            $table->foreignId('approved_by')->nullable(); // ID of the approver (nullable)
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};

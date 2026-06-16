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
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete(); // Class reference
            $table->foreignId('fee_type_id')->constrained('fee_types')->cascadeOnDelete(); // Fee type reference
            $table->string('receipt_number')->unique();
            $table->decimal('total_fees', 10, 2);
            $table->decimal('amount_paid', 10, 2);
            $table->string('month'); // e.g., "January", "February"
            $table->date('payment_date')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete(); // Assuming students are in users table
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete(); // Explicit table name
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('morning')->nullable();
            $table->boolean('afternoon')->nullable();
            $table->string('status', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_attendances');
    }
};

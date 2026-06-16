<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_plans', function (Blueprint $table) {
            $table->id();

            // FK: teachers are rows in users table
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // FK: subjects table
            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // FK: classes live in school_classes table (your code uses \App\Models\SchoolClass)
            $table->foreignId('class_id')
                ->constrained('school_classes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->date('date')->index();
            $table->string('topic_covered', 255);
            $table->unsignedSmallInteger('student_count'); // 0–65535 is plenty
            $table->text('details')->nullable();

            $table->timestamps();

            // Prevent duplicate plan rows for same teacher/subject/class/day
            $table->unique(['teacher_id', 'subject_id', 'class_id', 'date'], 'teacher_plans_unique_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_plans');
    }
};

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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('language_id');
            $table->foreignId('subject_id');
            $table->foreignId('difficulty_id');
            $table->enum('type', ['multiple_choice', 'true_false', 'written', 'fill_in_the_blank'])->default('written');
            $table->text('question_text');
            $table->text('explanation')->nullable(); // Explanation for correct answer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

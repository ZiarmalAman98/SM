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
        Schema::table('exam_results', function (Blueprint $table) {
            $table->integer('written_marks')->nullable()->after('marks');
            $table->integer('recital_marks')->nullable()->after('written_marks');
            $table->integer('homework_marks')->nullable()->after('recital_marks');
            $table->integer('class_activity_marks')->nullable()->after('homework_marks');
            $table->string('mark_in_words')->nullable()->after('class_activity_marks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropColumn([
                'written_marks',
                'recital_marks',
                'homework_marks',
                'class_activity_marks',
                'mark_in_words'
            ]);
        });
    }
};

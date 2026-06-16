<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_classes', function (Blueprint $table) {
            $table->foreignId('fee_type_id')
                ->nullable()
                ->after('class_id')
                ->constrained('fee_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_classes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fee_type_id');
        });
    }
};

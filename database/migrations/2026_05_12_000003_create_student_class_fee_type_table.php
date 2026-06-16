<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_class_fee_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_class_id')->constrained('student_classes')->cascadeOnDelete();
            $table->foreignId('fee_type_id')->constrained('fee_types')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['student_class_id', 'fee_type_id']);
        });

        if (Schema::hasColumn('student_classes', 'fee_type_id')) {
            DB::table('student_classes')
                ->whereNotNull('fee_type_id')
                ->orderBy('id')
                ->each(function ($studentClass): void {
                    DB::table('student_class_fee_type')->insertOrIgnore([
                        'student_class_id' => $studentClass->id,
                        'fee_type_id' => $studentClass->fee_type_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });

            Schema::table('student_classes', function (Blueprint $table) {
                $table->dropConstrainedForeignId('fee_type_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('student_classes', function (Blueprint $table) {
            $table->foreignId('fee_type_id')
                ->nullable()
                ->after('class_id')
                ->constrained('fee_types')
                ->nullOnDelete();
        });

        DB::table('student_class_fee_type')
            ->orderBy('id')
            ->each(function ($row): void {
                DB::table('student_classes')
                    ->where('id', $row->student_class_id)
                    ->whereNull('fee_type_id')
                    ->update(['fee_type_id' => $row->fee_type_id]);
            });

        Schema::dropIfExists('student_class_fee_type');
    }
};

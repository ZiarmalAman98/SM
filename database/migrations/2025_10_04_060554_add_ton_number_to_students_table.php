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
        // Check if ton_number column already exists before adding it
        if (!Schema::hasColumn('students', 'ton_number')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('ton_number')->nullable()->after('tazkira_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if ton_number column exists before dropping it
        if (Schema::hasColumn('students', 'ton_number')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('ton_number');
            });
        }
    }
};

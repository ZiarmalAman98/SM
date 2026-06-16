<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if destination column exists and drop it if it does
        if (Schema::hasColumn('daily_transfers', 'destination')) {
            Schema::table('daily_transfers', function (Blueprint $table) {
                $table->dropColumn('destination');
            });
        }

        // Check if destination_user_id column exists, if not add it
        if (!Schema::hasColumn('daily_transfers', 'destination_user_id')) {
            Schema::table('daily_transfers', function (Blueprint $table) {
                $table->unsignedBigInteger('destination_user_id')->nullable()->after('amount');
            });
        } else {
            // If column exists but is not nullable, make it nullable
            Schema::table('daily_transfers', function (Blueprint $table) {
                $table->unsignedBigInteger('destination_user_id')->nullable()->change();
            });
        }

        // Fix existing data: set invalid destination_user_id values to NULL
        // This handles the case where destination_user_id = 0 (which doesn't exist in users table)
        DB::table('daily_transfers')
            ->whereNotIn('destination_user_id', DB::table('users')->pluck('id'))
            ->update(['destination_user_id' => null]);

        // Check if foreign key constraint already exists before adding it
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'daily_transfers'
            AND COLUMN_NAME = 'destination_user_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (empty($foreignKeys)) {
            Schema::table('daily_transfers', function (Blueprint $table) {
                $table->foreign('destination_user_id')->references('id')->on('users');
            });
        }
    }

    public function down(): void
    {
        Schema::table('daily_transfers', function (Blueprint $table) {
            $table->dropForeign(['destination_user_id']);
            $table->dropColumn('destination_user_id');

            // Restore old string column if rolling back
            $table->string('destination');
        });
    }

};

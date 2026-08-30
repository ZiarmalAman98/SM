<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'source_type')) {
                $table->nullableMorphs('source');
            }

            if (! Schema::hasColumn('transactions', 'occurred_on')) {
                $table->date('occurred_on')->nullable()->after('description');
            }
        });

        DB::table('transactions')
            ->whereNull('occurred_on')
            ->update(['occurred_on' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'source_id')) {
                $table->dropMorphs('source');
            }

            if (Schema::hasColumn('transactions', 'occurred_on')) {
                $table->dropColumn('occurred_on');
            }
        });
    }
};

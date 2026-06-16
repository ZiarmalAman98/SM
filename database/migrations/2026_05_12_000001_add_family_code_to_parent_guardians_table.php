<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parent_guardians', function (Blueprint $table) {
            $table->string('family_code', 50)->nullable()->after('user_id');
        });

        DB::table('parent_guardians')
            ->orderBy('id')
            ->each(function ($parentGuardian): void {
                DB::table('parent_guardians')
                    ->where('id', $parentGuardian->id)
                    ->update([
                        'family_code' => 'FAM-' . str_pad((string) $parentGuardian->id, 6, '0', STR_PAD_LEFT),
                    ]);
            });

        Schema::table('parent_guardians', function (Blueprint $table) {
            $table->unique('family_code');
        });
    }

    public function down(): void
    {
        Schema::table('parent_guardians', function (Blueprint $table) {
            $table->dropUnique(['family_code']);
            $table->dropColumn('family_code');
        });
    }
};

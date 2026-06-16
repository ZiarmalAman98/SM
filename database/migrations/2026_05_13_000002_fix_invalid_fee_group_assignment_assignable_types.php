<?php

use App\Models\SchoolClass;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('fee_group_assignments')
            ->where('assignable_type', 'like', 'Assignable Type % for Fee Group Assignments')
            ->update(['assignable_type' => SchoolClass::class]);
    }

    public function down(): void
    {
        //
    }
};

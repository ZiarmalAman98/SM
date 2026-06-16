<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parent_invoice_items', function (Blueprint $table) {
            $table->foreignId('fee_group_assignment_id')
                ->nullable()
                ->after('fee_type_id')
                ->constrained('fee_group_assignments')
                ->nullOnDelete();
            $table->foreignId('fee_discount_id')
                ->nullable()
                ->after('fee_group_assignment_id')
                ->constrained('fee_discounts')
                ->nullOnDelete();
            $table->decimal('gross_amount', 12, 2)
                ->default(0)
                ->after('description');
            $table->decimal('discount_amount', 12, 2)
                ->default(0)
                ->after('gross_amount');
        });

        DB::table('parent_invoice_items')
            ->where('gross_amount', 0)
            ->update(['gross_amount' => DB::raw('amount')]);
    }

    public function down(): void
    {
        Schema::table('parent_invoice_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fee_discount_id');
            $table->dropConstrainedForeignId('fee_group_assignment_id');
            $table->dropColumn(['gross_amount', 'discount_amount']);
        });
    }
};

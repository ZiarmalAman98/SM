<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_suppliers', function (Blueprint $table) {
            $table->string('supplier_code')->nullable()->unique()->after('id');
            $table->string('contact_person')->nullable()->after('company_name');
            $table->string('alternate_phone')->nullable()->after('phone');
            $table->enum('opening_balance_type', ['payable', 'receivable'])->default('payable')->after('opening_balance');
            $table->boolean('is_active')->default(true)->after('opening_balance_type');
            $table->string('tax_number')->nullable()->after('is_active');
            $table->string('bank_account')->nullable()->after('tax_number');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'supplier_code',
                'contact_person',
                'alternate_phone',
                'opening_balance_type',
                'is_active',
                'tax_number',
                'bank_account',
            ]);
        });
    }
};

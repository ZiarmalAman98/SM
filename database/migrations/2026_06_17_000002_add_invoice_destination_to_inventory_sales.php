<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_sales', function (Blueprint $table) {
            $table->enum('sale_type', ['regular', 'admission'])->default('regular')->after('sale_no');
            $table->enum('payment_destination', ['pay_here', 'parent_invoice', 'unpaid'])->default('pay_here')->after('sale_type');
            $table->foreignId('parent_invoice_id')->nullable()->after('parent_guardian_id')->constrained('parent_invoices')->nullOnDelete();
        });

        Schema::table('parent_invoice_items', function (Blueprint $table) {
            $table->foreignId('inventory_sale_id')->nullable()->after('fee_discount_id')->constrained('inventory_sales')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('parent_invoice_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inventory_sale_id');
        });

        Schema::table('inventory_sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_invoice_id');
            $table->dropColumn(['sale_type', 'payment_destination']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_product_id')->constrained('inventory_products')->cascadeOnDelete();
            $table->enum('type', ['purchase_in', 'sale_out', 'adjustment_in', 'adjustment_out', 'return_in', 'return_out']);
            $table->decimal('quantity', 12, 2);
            $table->nullableMorphs('reference');
            $table->date('movement_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['inventory_product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_movements');
    }
};

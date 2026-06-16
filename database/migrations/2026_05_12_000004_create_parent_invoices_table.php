<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('parent_guardian_id')->constrained('parent_guardians')->cascadeOnDelete();
            $table->string('family_code', 50);
            $table->string('billing_month', 20);
            $table->unsignedSmallInteger('billing_year');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('previous_balance', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->enum('status', ['issued', 'partial', 'paid', 'cancelled'])->default('issued');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['parent_guardian_id', 'billing_year', 'billing_month'], 'parent_invoice_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_invoices');
    }
};

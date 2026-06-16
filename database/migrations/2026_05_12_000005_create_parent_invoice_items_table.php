<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_invoice_id')->constrained('parent_invoices')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_class_id')->nullable()->constrained('student_classes')->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('fee_type_id')->nullable()->constrained('fee_types')->nullOnDelete();
            $table->string('billing_month', 20);
            $table->unsignedSmallInteger('billing_year');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->boolean('is_previous_balance')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_invoice_items');
    }
};

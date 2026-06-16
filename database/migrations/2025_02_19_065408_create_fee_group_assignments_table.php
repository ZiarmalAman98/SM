<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fee_group_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_group_id');
            $table->morphs('assignable'); // Adds assignable_id and assignable_type
            $table->date('effective_date')->nullable();
            $table->unsignedBigInteger('fee_discount_id')->nullable();
            $table->timestamps();
            
            $table->foreign('fee_group_id')->references('id')->on('fee_groups')->onDelete('cascade');
            $table->foreign('fee_discount_id')->references('id')->on('fee_discounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_group_assignments');
    }
};

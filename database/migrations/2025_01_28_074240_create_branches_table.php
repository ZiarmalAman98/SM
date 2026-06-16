<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name');
            $table->text('branch_description')->nullable();
            $table->string('branch_status')->default('active');
            $table->string('branch_address')->nullable();
            $table->string('branch_phone')->nullable();
            $table->string('branch_email')->nullable();
            $table->string('branch_website')->nullable();
            $table->string('branch_manager_name')->nullable();
            $table->decimal('monthly_budget', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};

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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->timestamp('complaint_date')->useCurrent();
            
            // Polymorphic relationship for the complainant (nullable)
            $table->string('complainant_type')->nullable();
            $table->unsignedBigInteger('complainant_id')->nullable();
            
            // Polymorphic relationship for the correspondent (nullable)
            $table->string('correspondent_type')->nullable();
            $table->unsignedBigInteger('correspondent_id')->nullable();
            
            $table->string('subject', 150);
            $table->text('description');
            $table->enum('status', ['New', 'In Review', 'Resolved', 'Closed'])->default('New');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

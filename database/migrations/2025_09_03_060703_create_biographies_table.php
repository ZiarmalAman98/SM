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
        Schema::create('biographies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('father_name');
            $table->string('grand_father_name');
            $table->string('family_name')->nullable();
            $table->integer('age')->nullable();
            $table->string('citizenship_tazkira')->nullable();
            $table->string('nationality');
            $table->string('father_occupation');
            $table->string('mother_language');
            $table->string('permanent_province')->nullable();
            $table->string('permanent_district')->nullable();
            $table->string('permanent_village')->nullable();
            $table->string('current_province')->nullable();
            $table->string('current_district')->nullable();
            $table->string('current_village')->nullable();
            $table->string('brother_name')->nullable();
            $table->string('uncle_name')->nullable();
            $table->string('maternal_uncle_name')->nullable();
            $table->string('maternal_uncle_son_name')->nullable();
            $table->string('paternal_uncle_son_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biographies');
    }
};

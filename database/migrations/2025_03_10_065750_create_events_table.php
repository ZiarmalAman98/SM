<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // Primary key "id"
            $table->string('title');
            $table->string('color');
            $table->text('description')->nullable(); // Event description, optional
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamps(); // "created_at" and "updated_at"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

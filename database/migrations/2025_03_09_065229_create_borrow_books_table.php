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
        Schema::create('borrow_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); // User borrowing the book
            $table->foreignId('addBook_id'); // The borrowed book (renamed from book_id)
            $table->date('borrow_date');   // Date book was borrowed
            $table->date('return_date')->nullable();   // Expected return date
            $table->text('description')->nullable(); // Additional notes or details
            $table->timestamps(); // Created at / Updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_books');
    }
};

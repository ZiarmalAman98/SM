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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('designation');
            $table->string('department')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();

            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable();

            $table->date('date_of_birth')->nullable();
            $table->date('joining_date')->nullable();

            $table->string('photo')->nullable(); // Store file path

            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();

            $table->string('qualification')->nullable();
            $table->text('work_experience')->nullable();

            $table->text('note')->nullable();

            $table->decimal('basic_salary', 10, 2);

            $table->enum('contract_type', ['Permanent', 'Temporary', 'Intern'])->nullable();

            $table->time('work_from')->nullable();
            $table->time('work_to')->nullable();

            $table->string('title')->nullable();

            // Bank Details
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_branch')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};

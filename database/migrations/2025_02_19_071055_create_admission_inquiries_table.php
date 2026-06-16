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
        Schema::create('admission_inquiries', function (Blueprint $table) {
            $table->id();
            $table->timestamp('inquiry_date')->useCurrent();
            $table->string('parent_name', 100);
            $table->string('phone_number', 20);
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('child_name', 100)->nullable();
            $table->date('child_dob')->nullable();
            $table->unsignedBigInteger('interested_class_id');
            $table->enum('inquiry_status', ['Pending', 'Reviewed', 'Accepted', 'Rejected'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('interested_class_id')->references('id')->on('school_classes')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_inquiries');
    }
};

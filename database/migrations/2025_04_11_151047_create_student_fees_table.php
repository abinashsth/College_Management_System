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
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_structure_id')->constrained('fee_structures');
            $table->foreignId('fee_component_id')->constrained('fee_components');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('pending'); // pending, paid, partially_paid, overdue, waived
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};

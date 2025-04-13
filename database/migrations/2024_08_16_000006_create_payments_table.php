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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no')->unique();
            $table->foreignId('invoice_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'online_payment', 'other'])->default('cash');
            $table->date('payment_date');
            $table->string('transaction_id')->nullable(); // External transaction reference
            $table->string('receipt_no')->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}; 
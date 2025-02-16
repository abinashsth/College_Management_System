<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee')->onDelete('cascade');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('allowances', 10, 2)->nullable();
            $table->decimal('deductions', 10, 2)->nullable();
            $table->decimal('net_salary', 10, 2)->storedAs('basic_salary + COALESCE(allowances, 0) - COALESCE(deductions, 0)');
            $table->date('payment_date')->default(now());
            $table->string('payment_method')->nullable()->default('cash');
            $table->enum('status', ['paid', 'unpaid', 'pending', 'rejected', 'approved'])->default('unpaid');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};

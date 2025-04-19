<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('allowances', 12, 2)->default(0.00);
            $table->decimal('deductions', 12, 2)->default(0.00);
            $table->decimal('net_salary', 12, 2);
            $table->date('payment_date');
            $table->enum('payment_status', ['pending', 'completed'])->default('pending');
          
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes for better performance
            $table->index('payment_date');
            $table->index('payment_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('salaries');
    }
};
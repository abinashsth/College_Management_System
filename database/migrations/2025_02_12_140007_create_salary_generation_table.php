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
        if (!Schema::hasTable('salary_sheets')) {
            Schema::create('salary_sheets', function (Blueprint $table) {
                $table->id();
                $table->string('month');    
                $table->date('payment_date');
              
                $table->foreignId('employee_id')->constrained('employees');
                $table->decimal('basic_salary', 10, 2);
                $table->decimal('allowance', 10, 2)->default(0);
                $table->decimal('deduction', 10, 2)->default(0);
                $table->decimal('total_salary', 10, 2);
                $table->enum('status', ['pending', 'paid'])->default('pending');    
                $table->string('description')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::dropIfExists('salary_sheets');
    }
};

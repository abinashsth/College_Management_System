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
        if (!Schema::hasTable('salary_increments')) {
            Schema::create('salary_increments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->decimal('current_salary', 10, 2)->comment('Current salary before increment');
                $table->decimal('increment_amount', 10, 2)->comment('Amount of increment');
                $table->decimal('new_salary', 10, 2)->comment('Salary after increment');
                $table->date('effective_date')->comment('Date from which increment is effective');
                $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Paid'])->default('Pending');
                $table->text('remarks')->nullable()->comment('Additional notes');
                $table->foreignId('created_by')->nullable()->constrained('users')->comment('User who created the record');
                $table->foreignId('updated_by')->nullable()->constrained('users')->comment('User who last updated the record');
                $table->foreignId('approved_by')->nullable()->constrained('users')->comment('User who approved the increment');
                $table->timestamps();
                $table->softDeletes();
                
                // Add indexes for better query performance
                $table->index('effective_date');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_increments');
    }
};

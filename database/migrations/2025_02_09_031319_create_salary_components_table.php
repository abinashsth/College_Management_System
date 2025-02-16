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
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the salary component
            $table->string('type'); // Type of the salary component (e.g., Allowance, Deduction)
            $table->decimal('amount', 10, 2); // Amount of the salary component
            $table->boolean('status'); // Status of the salary component (e.g., Active, Inactive)
          
            $table->timestamps(); // Created at and updated at timestamps
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};

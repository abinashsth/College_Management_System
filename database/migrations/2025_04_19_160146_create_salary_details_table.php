<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_id');
            $table->enum('type', ['allowance', 'deduction']);
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('salary_id')->references('id')->on('salaries')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_details');
    }
};

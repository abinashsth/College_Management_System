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
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('semester')->nullable();
            $table->integer('year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_core')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure a subject is only added once to a class per semester and year
            $table->unique(['class_id', 'subject_id', 'semester', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
}; 
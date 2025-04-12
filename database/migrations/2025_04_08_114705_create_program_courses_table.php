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
        Schema::create('program_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('academic_structures')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('semester')->nullable();
            $table->integer('year')->nullable();
            $table->boolean('is_elective')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
            
            // Ensure a course is only added once to a program per semester and year
            $table->unique(['program_id', 'course_id', 'semester', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_courses');
    }
};

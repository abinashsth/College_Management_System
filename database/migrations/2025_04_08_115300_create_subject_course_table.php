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
        Schema::create('subject_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('semester')->nullable();
            $table->integer('year')->nullable();
            $table->boolean('is_core')->default(true);
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure a subject is only added once to a course per semester and year
            $table->unique(['subject_id', 'course_id', 'semester', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_course');
    }
};

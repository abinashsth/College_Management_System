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
        Schema::create('student_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->dateTime('submitted_at')->nullable();
            $table->string('submission_file_path')->nullable();
            $table->text('submission_text')->nullable();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users');
            $table->dateTime('graded_at')->nullable();
            $table->enum('status', ['assigned', 'submitted', 'late', 'graded', 'returned'])->default('assigned');
            $table->boolean('is_late')->default(false);
            $table->timestamps();
            
            // Create a unique constraint to prevent duplicate assignments for students
            $table->unique(['student_id', 'assignment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assignments');
    }
};

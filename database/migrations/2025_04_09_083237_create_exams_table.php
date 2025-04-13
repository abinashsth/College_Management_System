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
        // Skip if table already exists (it's created by an earlier migration)
        if (Schema::hasTable('exams')) {
            return;
        }

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('exam_date');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('academic_session_id');
            $table->enum('exam_type', ['midterm', 'final', 'quiz', 'assignment', 'project', 'other'])->default('midterm');
            $table->string('semester')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location')->nullable();
            $table->string('room_number')->nullable();
            $table->integer('total_marks');
            $table->integer('passing_marks');
            $table->date('registration_deadline')->nullable();
            $table->date('result_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->decimal('weight_percentage', 5, 2)->nullable();
            $table->string('grading_scale')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Add indexes for common query patterns
            $table->index(['subject_id', 'exam_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop if we didn't create it (it was created by earlier migration)
        if (!Schema::hasTable('exams_created_by_earlier_migration')) {
            Schema::dropIfExists('exams');
        }
    }
};

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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'excused', 'sick_leave'])->default('present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->foreignId('taken_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Create unique constraint to prevent duplicate attendance entries
            $table->unique(['student_id', 'section_id', 'subject_id', 'attendance_date'], 'attendance_unique_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

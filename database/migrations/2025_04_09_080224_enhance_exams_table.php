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
        Schema::table('exams', function (Blueprint $table) {
            // Remove subject column if it exists (since we already have subject_id)
            if (Schema::hasColumn('exams', 'subject')) {
                $table->dropColumn('subject');
            }
            
            // Skip adding subject_id as it already exists in the original migration
            
            // Add academic session reference if it doesn't exist
            if (!Schema::hasColumn('exams', 'academic_session_id')) {
                $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            }
            
            // Add exam type and semester if they don't exist
            if (!Schema::hasColumn('exams', 'exam_type')) {
                $table->enum('exam_type', ['midterm', 'final', 'quiz', 'assignment', 'project', 'other'])->default('midterm');
            }
            
            if (!Schema::hasColumn('exams', 'semester')) {
                $table->string('semester')->nullable();
            }
            
            // Add duration and additional date/time fields if they don't exist
            if (!Schema::hasColumn('exams', 'duration_minutes')) {
                $table->integer('duration_minutes')->default(60)->after('exam_date');
            }
            
            if (!Schema::hasColumn('exams', 'start_time')) {
                $table->time('start_time')->nullable()->after('duration_minutes');
            }
            
            if (!Schema::hasColumn('exams', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            
            // Add location details if they don't exist
            if (!Schema::hasColumn('exams', 'location')) {
                $table->string('location')->nullable();
            }
            
            if (!Schema::hasColumn('exams', 'room_number')) {
                $table->string('room_number')->nullable();
            }
            
            // Add preparation and result related fields if they don't exist
            if (!Schema::hasColumn('exams', 'registration_deadline')) {
                $table->date('registration_deadline')->nullable();
            }
            
            if (!Schema::hasColumn('exams', 'result_date')) {
                $table->date('result_date')->nullable();
            }
            
            if (!Schema::hasColumn('exams', 'is_published')) {
                $table->boolean('is_published')->default(false);
            }
            
            // Add weighting and grading fields if they don't exist
            if (!Schema::hasColumn('exams', 'weight_percentage')) {
                $table->decimal('weight_percentage', 5, 2)->default(100.00);
            }
            
            if (!Schema::hasColumn('exams', 'grading_scale')) {
                $table->string('grading_scale')->nullable();
            }
            
            // Add administrative fields if they don't exist
            if (!Schema::hasColumn('exams', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('exams', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            }
            
            // Rename status field to be more descriptive if it exists
            if (Schema::hasColumn('exams', 'status') && !Schema::hasColumn('exams', 'is_active')) {
                $table->boolean('is_active')->default(true);
                $table->dropColumn('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // No need to drop subject_id as we didn't add it
            
            // Remove other fields we added
            if (Schema::hasColumn('exams', 'academic_session_id')) {
                $table->dropForeign(['academic_session_id']);
                $table->dropColumn('academic_session_id');
            }
            
            if (Schema::hasColumn('exams', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            
            if (Schema::hasColumn('exams', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
            
            // Drop columns we added
            $table->dropIfExists([
                'exam_type',
                'semester',
                'duration_minutes',
                'start_time',
                'end_time',
                'location',
                'room_number',
                'registration_deadline',
                'result_date',
                'is_published',
                'weight_percentage',
                'grading_scale',
                'is_active'
            ]);
            
            // Restore original columns if needed
            if (!Schema::hasColumn('exams', 'subject')) {
                $table->string('subject')->nullable();
            }
            
            if (!Schema::hasColumn('exams', 'status') && Schema::hasColumn('exams', 'is_active')) {
                $table->boolean('status')->default(true);
            }
        });
    }
};

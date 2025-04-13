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
            // Replace subject string with foreign key to subjects table
            if (Schema::hasColumn('exams', 'subject')) {
                $table->dropColumn('subject');
            }
            $table->foreignId('subject_id')->nullable()->after('class_id')->constrained('subjects')->nullOnDelete();
            
            // Add academic session reference
            $table->foreignId('academic_session_id')->nullable()->after('subject_id')->constrained('academic_sessions')->nullOnDelete();
            
            // Add exam type and semester
            $table->enum('exam_type', ['midterm', 'final', 'quiz', 'assignment', 'project', 'other'])->default('midterm')->after('academic_session_id');
            $table->string('semester')->nullable()->after('exam_type');
            
            // Add duration and additional date/time fields
            $table->integer('duration_minutes')->default(60)->after('exam_date');
            $table->time('start_time')->nullable()->after('duration_minutes');
            $table->time('end_time')->nullable()->after('start_time');
            
            // Add location details
            $table->string('location')->nullable()->after('end_time');
            $table->string('room_number')->nullable()->after('location');
            
            // Add preparation and result related fields
            $table->date('registration_deadline')->nullable()->after('room_number');
            $table->date('result_date')->nullable()->after('registration_deadline');
            $table->boolean('is_published')->default(false)->after('result_date');
            
            // Add weighting and grading fields
            $table->decimal('weight_percentage', 5, 2)->default(100.00)->after('is_published');
            $table->string('grading_scale')->nullable()->after('weight_percentage');
            
            // Add administrative fields
            $table->foreignId('created_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            
            // Rename status field to be more descriptive
            if (Schema::hasColumn('exams', 'status')) {
                $table->boolean('is_active')->default(true)->after('weight_percentage');
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
            // Remove new fields
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['academic_session_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            
            $table->dropColumn([
                'subject_id',
                'academic_session_id',
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
                'is_active',
                'created_by',
                'updated_by'
            ]);
            
            // Restore original columns
            $table->string('subject')->nullable();
            $table->boolean('status')->default(true);
        });
    }
};

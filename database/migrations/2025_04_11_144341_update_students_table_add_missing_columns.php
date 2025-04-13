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
        Schema::table('students', function (Blueprint $table) {
            // Check and add columns that might be missing
            if (!Schema::hasColumn('students', 'first_name')) {
                $table->string('first_name')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('students', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            
            if (!Schema::hasColumn('students', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('last_name');
            }
            
            if (!Schema::hasColumn('students', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
            
            if (!Schema::hasColumn('students', 'student_id')) {
                $table->string('student_id')->nullable()->unique()->after('user_id');
            }
            
            if (!Schema::hasColumn('students', 'registration_number')) {
                $table->string('registration_number')->nullable()->unique()->after('student_id');
            }
            
            if (!Schema::hasColumn('students', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('students', 'student_address')) {
                $table->text('student_address')->nullable()->after('phone_number');
            }
            
            if (!Schema::hasColumn('students', 'city')) {
                $table->string('city')->nullable()->after('student_address');
            }
            
            if (!Schema::hasColumn('students', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('students', 'program_id')) {
                $table->unsignedBigInteger('program_id')->nullable()->after('profile_photo');
            }
            
            if (!Schema::hasColumn('students', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('program_id');
            }
            
            if (!Schema::hasColumn('students', 'admission_date')) {
                $table->date('admission_date')->nullable()->after('class_id');
            }
            
            if (!Schema::hasColumn('students', 'batch_year')) {
                $table->string('batch_year')->nullable()->after('admission_date');
            }
            
            if (!Schema::hasColumn('students', 'years_of_study')) {
                $table->integer('years_of_study')->nullable()->after('batch_year');
            }
            
            if (!Schema::hasColumn('students', 'guardian_name')) {
                $table->string('guardian_name')->nullable()->after('current_semester');
            }
            
            if (!Schema::hasColumn('students', 'guardian_relation')) {
                $table->string('guardian_relation')->nullable()->after('guardian_name');
            }
            
            if (!Schema::hasColumn('students', 'guardian_contact')) {
                $table->string('guardian_contact')->nullable()->after('guardian_relation');
            }
            
            if (!Schema::hasColumn('students', 'guardian_email')) {
                $table->string('guardian_email')->nullable()->after('guardian_contact');
            }
            
            if (!Schema::hasColumn('students', 'guardian_address')) {
                $table->text('guardian_address')->nullable()->after('guardian_email');
            }
            
            if (!Schema::hasColumn('students', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('dob');
            }
            
            if (!Schema::hasColumn('students', 'enrollment_status')) {
                $table->enum('enrollment_status', ['active', 'inactive', 'graduated', 'expelled', 'suspended', 'admitted'])->default('active')->after('remarks');
            }
            
            if (!Schema::hasColumn('students', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('documents_verified_at')->constrained('users')->onDelete('set null');
            }
            
            // Add indexes for better query performance
            if (!Schema::hasIndex('students', 'students_email_index')) {
                $table->index('email');
            }
            
            if (Schema::hasColumn('students', 'enrollment_status') && !Schema::hasIndex('students', 'students_enrollment_status_index')) {
                $table->index('enrollment_status');
            }
            
            if (Schema::hasColumn('students', 'first_name') && Schema::hasColumn('students', 'last_name') && 
                !Schema::hasIndex('students', 'students_first_name_last_name_index')) {
                $table->index(['first_name', 'last_name']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop these columns because it would lose data
        // If you need to reverse this migration, implement it manually
    }
};

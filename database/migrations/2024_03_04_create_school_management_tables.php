<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Academic Setup - House Groups
        Schema::create('house_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Student House Assignment
        Schema::create('student_house_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('house_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Staff Profiles
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('designation');
            $table->date('joining_date');
            $table->string('qualification');
            $table->string('specialization')->nullable();
            $table->text('experience')->nullable();
            $table->string('contact_number');
            $table->string('emergency_contact');
            $table->text('address');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Guardian Profiles
        Schema::create('guardian_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('relationship');
            $table->string('occupation')->nullable();
            $table->string('contact_number');
            $table->string('emergency_contact');
            $table->text('address');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Student Guardian Relationships
        Schema::create('student_guardian_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('guardian_profile_id')->constrained()->onDelete('cascade');
            $table->string('relationship_type');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // Student Events
        Schema::create('student_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->text('description');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Student Event Participation
        Schema::create('student_event_participation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_event_id')->constrained()->onDelete('cascade');
            $table->string('participation_type');
            $table->string('status');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // ECA/Portfolio Categories
        Schema::create('eca_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Student ECA/Portfolio Marks
        Schema::create('student_eca_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('eca_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            $table->decimal('marks', 5, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Attendance Records
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused']);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Terminal Marks Ledger
        Schema::create('terminal_marks_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_type_id')->constrained()->onDelete('cascade');
            $table->decimal('total_marks', 8, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade');
            $table->integer('rank')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Final Grade Sheets
        Schema::create('final_grade_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            $table->decimal('total_marks', 8, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade');
            $table->integer('rank')->nullable();
            $table->decimal('attendance_percentage', 5, 2);
            $table->decimal('eca_average', 5, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('final_grade_sheets');
        Schema::dropIfExists('terminal_marks_ledger');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('student_eca_marks');
        Schema::dropIfExists('eca_categories');
        Schema::dropIfExists('student_event_participation');
        Schema::dropIfExists('student_events');
        Schema::dropIfExists('student_guardian_relationships');
        Schema::dropIfExists('guardian_profiles');
        Schema::dropIfExists('staff_profiles');
        Schema::dropIfExists('student_house_assignments');
        Schema::dropIfExists('house_groups');
    }
}; 
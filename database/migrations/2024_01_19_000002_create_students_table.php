<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('student_id')->nullable()->unique();
            $table->string('registration_number')->nullable()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->text('student_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->date('dob')->nullable();
            $table->string('profile_photo')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->onDelete('set null');
            $table->date('admission_date')->nullable();
            $table->string('batch_year')->nullable();
            $table->integer('years_of_study')->nullable();
            $table->string('current_semester')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_contact')->nullable();
            $table->string('guardian_email')->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->text('previous_education')->nullable();
            $table->string('last_qualification')->nullable();
            $table->string('last_qualification_marks')->nullable();
            $table->text('medical_information')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('enrollment_status', ['active', 'inactive', 'graduated', 'expelled', 'suspended', 'admitted'])->default('active');
            $table->string('fee_status')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('documents_verified_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Create indexes for commonly queried fields
            $table->index('email');
            $table->index('enrollment_status');
            $table->index(['first_name', 'last_name']);
        });

        // We'll add foreign keys later for tables that might not exist yet
        $this->addForeignKeysIfTableExists();
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }

    private function addForeignKeysIfTableExists()
    {
        // Add foreign keys for programs if table exists
        if (Schema::hasTable('programs')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
            });
        }

        // Add foreign keys for departments if table exists
        if (Schema::hasTable('departments')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            });
        }

        // Add foreign keys for sections if table exists
        if (Schema::hasTable('sections')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
            });
        }
    }
}; 
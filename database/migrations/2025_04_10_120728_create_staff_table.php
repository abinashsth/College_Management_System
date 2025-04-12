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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('staff_id')->unique()->comment('Employee ID');
            $table->enum('type', ['teaching', 'non-teaching', 'administrative'])->default('teaching');
            $table->string('designation')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->date('joining_date');
            $table->enum('employment_status', ['full-time', 'part-time', 'contract', 'visiting', 'other'])->default('full-time');
            $table->enum('employment_type', ['permanent', 'temporary', 'probation', 'contract', 'intern'])->default('permanent');
            
            // Contact information
            $table->string('contact_number')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            
            // Basic personal details
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('blood_group')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            
            // Professional information
            $table->text('biography')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->string('previous_institution')->nullable();
            
            // Administrative information
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('tax_id')->nullable();
            
            // Status tracking
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('profile_picture')->nullable();
            
            // System fields
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
}; 
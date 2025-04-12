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
            // --- Handle Pre-existing/Conflicting Columns ---

            // Drop original conflicting columns if they exist
            if (Schema::hasColumn('students', 'name')) {
                // Add new name columns first, potentially after 'id' or 'student_id'
                 if (Schema::hasColumn('students', 'student_id')) {
                    $table->string('first_name')->nullable()->after('student_id');
                } else {
                     $table->string('first_name')->nullable()->after('id');
                }
                $table->string('last_name')->nullable()->after('first_name');
                // Now drop the old 'name' column
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('students', 'address')) {
                $table->dropColumn('address'); // Will be replaced by guardian_address potentially, or needs separate address fields
            }
             if (Schema::hasColumn('students', 'contact_number')) {
                $table->dropColumn('contact_number'); // Replaced by specific phone/emergency contacts
            }
            if (Schema::hasColumn('students', 'dob')) {
                 // Rename dob to date_of_birth if necessary, or drop if adding new date field
                 // Let's assume we want to add a new date_of_birth, so drop dob
                 $table->dropColumn('dob');
            }
             if (Schema::hasColumn('students', 'status')) {
                 $table->dropColumn('status'); // Replaced by enrollment_status enum
             }
             if (Schema::hasColumn('students', 'verified_at')) {
                 $table->dropColumn('verified_at'); // Replaced by documents_verified_at
             }

            // --- Add New Columns (with checks) ---

            // Basic Information
            if (!Schema::hasColumn('students', 'student_id')) {
            $table->string('student_id')->unique()->nullable()->after('id');
            }
            // first_name, last_name handled above

             if (!Schema::hasColumn('students', 'gender')) {
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('last_name');
             }
             if (!Schema::hasColumn('students', 'profile_photo')) {
            $table->string('profile_photo')->nullable()->after('gender');
             }
             // Add date_of_birth (replacement for dob)
             if (!Schema::hasColumn('students', 'date_of_birth')) {
                 $table->date('date_of_birth')->nullable()->after('profile_photo'); // Adjust position as needed
             }

            // Contact Information (assuming email is kept from original migration)
             if (!Schema::hasColumn('students', 'phone_number')) {
                 $table->string('phone_number')->nullable()->after('email'); // Add primary phone
             }
             if (!Schema::hasColumn('students', 'emergency_contact_name')) {
                 $table->string('emergency_contact_name')->nullable()->after('phone_number');
             }
            if (!Schema::hasColumn('students', 'emergency_contact_number')) {
            $table->string('emergency_contact_number')->nullable()->after('emergency_contact_name');
            }
             if (!Schema::hasColumn('students', 'emergency_contact_relationship')) {
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_number');
             }
            
            // Academic Information (assuming class_id exists from original migration)
            if (!Schema::hasColumn('students', 'program_id')) {
            $table->foreignId('program_id')->nullable()->after('class_id')->constrained('programs')->nullOnDelete();
            }
             if (!Schema::hasColumn('students', 'department_id')) {
                 $table->foreignId('department_id')->nullable()->after('program_id')->constrained('departments')->nullOnDelete();
             }
             if (!Schema::hasColumn('students', 'batch_year')) {
            $table->integer('batch_year')->nullable()->after('department_id');
             }
             if (!Schema::hasColumn('students', 'admission_number')) {
            $table->string('admission_number')->unique()->nullable()->after('batch_year');
             }
             if (!Schema::hasColumn('students', 'admission_date')) {
            $table->date('admission_date')->nullable()->after('admission_number');
             }
             if (!Schema::hasColumn('students', 'current_semester')) {
            $table->string('current_semester')->nullable()->after('admission_date');
             }
            if (!Schema::hasColumn('students', 'academic_session_id')) {
            $table->foreignId('academic_session_id')->nullable()->after('current_semester')->constrained('academic_sessions')->nullOnDelete();
             }
            
            // Guardian Information
             if (!Schema::hasColumn('students', 'guardian_name')) {
            $table->string('guardian_name')->nullable()->after('emergency_contact_relationship');
             }
             if (!Schema::hasColumn('students', 'guardian_relation')) {
            $table->string('guardian_relation')->nullable()->after('guardian_name');
             }
            if (!Schema::hasColumn('students', 'guardian_contact')) {
            $table->string('guardian_contact')->nullable()->after('guardian_relation');
             }
             // Add student's own address field if needed, separate from guardian
             if (!Schema::hasColumn('students', 'student_address')) {
                 $table->text('student_address')->nullable()->after('guardian_contact'); // Example placement
             }
             if (!Schema::hasColumn('students', 'guardian_address')) {
                 $table->string('guardian_address')->nullable()->after('student_address');
             }
             if (!Schema::hasColumn('students', 'guardian_occupation')) {
            $table->string('guardian_occupation')->nullable()->after('guardian_address');
             }
            
            // Additional Fields
             if (!Schema::hasColumn('students', 'previous_education')) {
            $table->text('previous_education')->nullable()->after('guardian_occupation');
             }
             if (!Schema::hasColumn('students', 'medical_information')) {
            $table->text('medical_information')->nullable()->after('previous_education');
             }
             if (!Schema::hasColumn('students', 'remarks')) {
            $table->text('remarks')->nullable()->after('medical_information');
             }
             if (!Schema::hasColumn('students', 'documents')) {
            $table->json('documents')->nullable()->after('remarks');
             }
            // Add enrollment_status (replacement for boolean status)
             if (!Schema::hasColumn('students', 'enrollment_status')) {
            $table->enum('enrollment_status', [
                'applied', 'admitted', 'active', 'inactive', 'graduated', 'transferred', 'withdrawn', 'expelled'
                 ])->default('applied')->after('documents'); // Adjust position
             }
             // Add documents_verified_at (replacement for verified_at)
             if (!Schema::hasColumn('students', 'documents_verified_at')) {
                 $table->timestamp('documents_verified_at')->nullable()->after('enrollment_status'); // Adjust position
             }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // --- Restore Original Columns & Drop New Ones ---

            // Restore original name column if first/last name exist
            if (Schema::hasColumns('students', ['first_name', 'last_name']) && !Schema::hasColumn('students', 'name')) {
                 $table->string('name')->nullable()->after('id'); // Or determine original position
                 $table->dropColumn(['first_name', 'last_name']);
             } elseif (Schema::hasColumn('students', 'first_name')) {
                 $table->dropColumn('first_name');
             } elseif (Schema::hasColumn('students', 'last_name')) {
                 $table->dropColumn('last_name');
             }

            // Restore original address if student_address exists
             if (Schema::hasColumn('students', 'student_address') && !Schema::hasColumn('students', 'address')) {
                 $table->string('address')->nullable(); // Add back original, adjust type/position if known
                 // $table->dropColumn('student_address'); // Keep drop separate
             }
            // Restore original contact_number if phone_number exists
             if (Schema::hasColumn('students', 'phone_number') && !Schema::hasColumn('students', 'contact_number')) {
                 $table->string('contact_number')->nullable();
                 // $table->dropColumn('phone_number'); // Keep drop separate
             }
             // Restore original dob if date_of_birth exists
             if (Schema::hasColumn('students', 'date_of_birth') && !Schema::hasColumn('students', 'dob')) {
                 $table->date('dob')->nullable();
                 // $table->dropColumn('date_of_birth'); // Keep drop separate
             }
             // Restore original status if enrollment_status exists
            if (Schema::hasColumn('students', 'enrollment_status') && !Schema::hasColumn('students', 'status')) {
                 $table->boolean('status')->default(true);
                 // $table->dropColumn('enrollment_status'); // Keep drop separate
             }
            // Restore original verified_at if documents_verified_at exists
             if (Schema::hasColumn('students', 'documents_verified_at') && !Schema::hasColumn('students', 'verified_at')) {
                 $table->timestamp('verified_at')->nullable();
                 // $table->dropColumn('documents_verified_at'); // Keep drop separate
             }


            // Drop foreign keys first if they exist
            // Note: Need to check if constraint exists, name might vary
            // Using column name convention for simplicity, adjust if needed
            if (Schema::hasColumn('students', 'program_id')) $table->dropForeign(['students_program_id_foreign']);
            if (Schema::hasColumn('students', 'department_id')) $table->dropForeign(['students_department_id_foreign']);
            if (Schema::hasColumn('students', 'academic_session_id')) $table->dropForeign(['students_academic_session_id_foreign']);

            // Drop all potentially added columns (check existence before dropping)
            $columnsToDrop = [
                'student_id', 'gender', 'profile_photo', 'date_of_birth', // Basic info
                'phone_number', 'emergency_contact_name', 'emergency_contact_number', 'emergency_contact_relationship', // Contact
                'program_id', 'department_id', 'batch_year', 'admission_number', 'admission_date', 'current_semester', 'academic_session_id', // Academic
                'guardian_name', 'guardian_relation', 'guardian_contact', 'student_address', 'guardian_address', 'guardian_occupation', // Guardian/Address
                'previous_education', 'medical_information', 'remarks', 'documents', // Additional
                'enrollment_status', 'documents_verified_at' // Status/Verification
            ];
            foreach ($columnsToDrop as $column) {
                 if (Schema::hasColumn('students', $column)) {
                     $table->dropColumn($column);
                 }
             }

            // Drop first/last name if they weren't handled by the 'name' restoration logic
             if (Schema::hasColumn('students', 'first_name')) $table->dropColumn('first_name');
             if (Schema::hasColumn('students', 'last_name')) $table->dropColumn('last_name');


            // Add back the original student_id if it was dropped and not present (unlikely scenario based on up())
             // if (!Schema::hasColumn('students', 'student_id') && ...) { ... }

        });
    }
};

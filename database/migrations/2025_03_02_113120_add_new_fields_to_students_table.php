<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // First add class_id if it doesn't exist
            if (!Schema::hasColumn('students', 'class_id')) {
                $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        // Then update class_id based on course_id
        if (Schema::hasColumn('students', 'class_id')) {
            DB::statement('
                UPDATE students s
                JOIN classes c ON c.course_id = s.course_id
                SET s.class_id = c.id
                WHERE s.class_id IS NULL
            ');
        }

        Schema::table('students', function (Blueprint $table) {
            // Add new fields if they don't exist
            if (!Schema::hasColumn('students', 'father_name')) {
                $table->string('father_name')->nullable();
            }
            if (!Schema::hasColumn('students', 'mother_name')) {
                $table->string('mother_name')->nullable();
            }
            if (!Schema::hasColumn('students', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }
            if (!Schema::hasColumn('students', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
            }
            if (!Schema::hasColumn('students', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('students', 'admission_date')) {
                $table->date('admission_date')->nullable();
            }

            // Rename existing columns to match new schema
            if (Schema::hasColumn('students', 'name') && !Schema::hasColumn('students', 'student_name')) {
                $table->renameColumn('name', 'student_name');
            }
            if (Schema::hasColumn('students', 'contact') && !Schema::hasColumn('students', 'phone')) {
                $table->renameColumn('contact', 'phone');
            }
            if (Schema::hasColumn('students', 'roll_number') && !Schema::hasColumn('students', 'roll_no')) {
                $table->renameColumn('roll_number', 'roll_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Reverse the column renames
            if (Schema::hasColumn('students', 'student_name')) {
                $table->renameColumn('student_name', 'name');
            }
            if (Schema::hasColumn('students', 'phone')) {
                $table->renameColumn('phone', 'contact');
            }
            if (Schema::hasColumn('students', 'roll_no')) {
                $table->renameColumn('roll_no', 'roll_number');
            }

            // Drop the new columns
            $table->dropColumn([
                'father_name',
                'mother_name',
                'date_of_birth',
                'gender',
                'address',
                'admission_date'
            ]);

            // Drop class_id if it exists
            if (Schema::hasColumn('students', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }
        });
    }
};

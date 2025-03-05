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
            // Drop existing columns only if they exist
            $columnsToCheck = ['name', 'contact', 'admission_number', 'roll_number'];
            $columnsToDrop = [];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
            
            // Add new columns if they don't exist
            if (!Schema::hasColumn('students', 'student_name')) {
                $table->string('student_name');
            }
            
            if (!Schema::hasColumn('students', 'father_name')) {
                $table->string('father_name');
            }
            
            if (!Schema::hasColumn('students', 'mother_name')) {
                $table->string('mother_name')->nullable();
            }
            
            if (!Schema::hasColumn('students', 'date_of_birth')) {
                $table->date('date_of_birth');
            }
            
            if (!Schema::hasColumn('students', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other']);
            }
            
            if (!Schema::hasColumn('students', 'address')) {
                $table->text('address');
            }
            
            if (!Schema::hasColumn('students', 'phone')) {
                $table->string('phone');
            }
            
            if (!Schema::hasColumn('students', 'admission_number') && !in_array('admission_number', $columnsToDrop)) {
                $table->string('admission_number')->unique();
            }
            
            if (!Schema::hasColumn('students', 'roll_no')) {
                $table->string('roll_no')->unique();
            }
            
            if (!Schema::hasColumn('students', 'admission_date')) {
                $table->date('admission_date');
            }
            
            // Add class_id if it doesn't exist
            if (!Schema::hasColumn('students', 'class_id')) {
                $table->foreignId('class_id')->constrained()->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a complex migration with many changes
        // For safety, we'll do nothing in the down method
        // as reversing this could cause data loss
    }
};

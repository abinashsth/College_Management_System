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
            if (Schema::hasColumn('students', 'student_name')) {
                $table->dropColumn('student_name');
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
            
            if (Schema::hasColumn('students', 'phone')) {
                $table->dropColumn('phone');
            }
            
            if (Schema::hasColumn('students', 'admission_number')) {
                $table->dropColumn('admission_number');
            }
            
            if (Schema::hasColumn('students', 'roll_number')) {
                $table->dropColumn('roll_number');
            }
            
            if (!Schema::hasColumn('students', 'admission_date')) {
                $table->date('admission_date');
            }
            
            if (!Schema::hasColumn('students', 'academic_session_id')) {
                $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            }
            
            // Add class_id if it doesn't exist
            if (!Schema::hasColumn('students', 'class_id')) {
                $table->foreignId('class_id')->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('students', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active');
            }
            
            if (!Schema::hasColumn('students', 'email')) {
                $table->string('email')->unique()->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn([
                'student_name',
                'father_name', 
                'mother_name',
                'date_of_birth',
                'gender',
                'address',
                'phone',
                'admission_number',
                'roll_no',
                'admission_date',
                'academic_session_id',
                'class_id',
                'status',
                'email'
            ]);
        });
    }
};

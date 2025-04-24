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
        // Fix exam_materials foreign keys
        Schema::table('exam_materials', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_materials', 'exam_id')) {
                $table->foreignId('exam_id')->after('id')->constrained()->onDelete('cascade');
            }
        });

        // Fix exam_supervisors foreign keys
        Schema::table('exam_supervisors', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_supervisors', 'exam_schedule_id')) {
                $table->foreignId('exam_schedule_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('exam_supervisors', 'user_id')) {
                $table->foreignId('user_id')->after('exam_schedule_id')->constrained()->onDelete('cascade');
            }
        });

        // Fix exam_rules foreign keys
        Schema::table('exam_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_rules', 'exam_id')) {
                $table->foreignId('exam_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('exam_rules', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            }
        });

        // Fix exam_student foreign keys
        Schema::table('exam_student', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_student', 'exam_id')) {
                $table->foreignId('exam_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('exam_student', 'student_id')) {
                $table->foreignId('student_id')->after('exam_id')->constrained()->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse as we're only adding missing constraints
    }
}; 
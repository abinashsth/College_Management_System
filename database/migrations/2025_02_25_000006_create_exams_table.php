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
        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                // Add new columns if they don't exist
                if (!Schema::hasColumn('exams', 'session_id')) {
                    $table->foreignId('session_id')->nullable()->after('id');
                    $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
                }
                if (!Schema::hasColumn('exams', 'total_marks')) {
                    $table->integer('total_marks')->default(100)->after('subject_id');
                }
                if (!Schema::hasColumn('exams', 'pass_marks')) {
                    $table->integer('pass_marks')->default(40)->after('total_marks');
                }
                if (!Schema::hasColumn('exams', 'status')) {
                    $table->boolean('status')->default(true)->after('pass_marks');
                }
                if (!Schema::hasColumn('exams', 'description')) {
                    $table->text('description')->nullable()->after('status');
                }

                // Add unique constraint
                try {
                    $table->unique(['session_id', 'class_id', 'subject_id'], 'exam_unique');
                } catch (\Exception $e) {
                    // If unique constraint already exists, skip it
                }
            });
        } else {
            Schema::create('exams', function (Blueprint $table) {
                $table->id();
                $table->foreignId('session_id')->nullable();
                $table->string('name');
                $table->date('exam_date');
                $table->foreignId('class_id')->constrained()->onDelete('cascade');
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->integer('total_marks')->default(100);
                $table->integer('pass_marks')->default(40);
                $table->boolean('status')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
                $table->unique(['session_id', 'class_id', 'subject_id'], 'exam_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
}; 
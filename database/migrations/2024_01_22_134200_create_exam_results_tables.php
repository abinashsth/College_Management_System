<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamResultsTables extends Migration
{
    public function up()
    {
        // Create exam_results table
        if (!Schema::hasTable('exam_results')) {
            Schema::create('exam_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained()->onDelete('cascade');
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->decimal('theory_marks', 5, 2)->nullable();
                $table->decimal('practical_marks', 5, 2)->nullable();
                $table->decimal('total_marks', 5, 2)->nullable();
                $table->string('grade')->nullable();
                $table->boolean('is_pass')->default(false);
                $table->text('remarks')->nullable();
                $table->timestamps();

                // Each student can have only one result per exam per subject
                $table->unique(['exam_id', 'student_id', 'subject_id']);
            });
        }

        // Create exam_result_summaries table
        if (!Schema::hasTable('exam_result_summaries')) {
            Schema::create('exam_result_summaries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained()->onDelete('cascade');
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->decimal('total_marks', 8, 2);
                $table->decimal('percentage', 5, 2);
                $table->string('grade');
                $table->integer('rank')->nullable();
                $table->boolean('is_pass');
                $table->text('remarks')->nullable();
                $table->timestamps();

                // Each student can have only one summary per exam
                $table->unique(['exam_id', 'student_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('exam_result_summaries');
        Schema::dropIfExists('exam_results');
    }
}

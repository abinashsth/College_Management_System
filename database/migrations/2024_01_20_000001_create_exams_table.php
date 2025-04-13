<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('exam_date');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('subject');
            $table->integer('total_marks');
            $table->integer('passing_marks');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_student');
        Schema::dropIfExists('exams');
    }
}; 
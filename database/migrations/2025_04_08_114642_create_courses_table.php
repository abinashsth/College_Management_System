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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('credit_hours');
            $table->integer('lecture_hours')->nullable();
            $table->integer('lab_hours')->nullable();
            $table->integer('tutorial_hours')->nullable();
            $table->string('level')->nullable(); // e.g., beginner, intermediate, advanced
            $table->string('type')->nullable(); // e.g., mandatory, elective
            $table->foreignId('department_id')->nullable()->constrained('academic_structures')->onDelete('set null');
            $table->string('status')->default('active'); // active, inactive, archived
            $table->text('learning_outcomes')->nullable();
            $table->text('evaluation_criteria')->nullable();
            $table->text('syllabus')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};

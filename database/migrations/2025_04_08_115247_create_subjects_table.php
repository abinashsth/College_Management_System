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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('credit_hours');
            $table->integer('lecture_hours')->nullable();
            $table->integer('practical_hours')->nullable();
            $table->integer('tutorial_hours')->nullable();
            $table->string('level')->nullable(); // beginner, intermediate, advanced
            $table->foreignId('department_id')->nullable()->constrained('academic_structures')->onDelete('set null');
            $table->string('semester_offered')->nullable(); // Comma-separated values: fall,spring,summer
            $table->text('learning_objectives')->nullable();
            $table->text('grading_policy')->nullable();
            $table->text('syllabus')->nullable();
            $table->text('reference_materials')->nullable();
            $table->text('teaching_methods')->nullable();
            $table->string('status')->default('active'); // active, inactive, archived
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

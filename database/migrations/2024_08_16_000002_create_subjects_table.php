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
        // Skip if the table already exists
        if (Schema::hasTable('subjects')) {
            return;
        }
        
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
            
            // Use unsignedBigInteger instead of direct foreign key
            $table->unsignedBigInteger('department_id')->nullable();
            
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
        
        // Add foreign key constraint only if the referenced table exists
        if (Schema::hasTable('subjects') && Schema::hasTable('academic_structures')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->foreign('department_id')
                    ->references('id')
                    ->on('academic_structures')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

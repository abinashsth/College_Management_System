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
        // Skip if table already exists
        if (Schema::hasTable('exam_materials')) {
            return;
        }
        
        Schema::create('exam_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->string('title');
            $table->enum('type', [
                'question_paper', 
                'answer_sheet', 
                'supplementary', 
                'instruction', 
                'resource', 
                'marking_scheme', 
                'other'
            ])->default('question_paper');
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable(); // Size in KB
            $table->text('description')->nullable();
            $table->boolean('is_for_students')->default(false);
            $table->boolean('is_for_teachers')->default(true);
            $table->boolean('is_confidential')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('release_date')->nullable();
            $table->integer('version')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it
        if (!Schema::hasTable('exam_materials_created_by_earlier_migration')) {
            Schema::dropIfExists('exam_materials');
        }
    }
};

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
        if (Schema::hasTable('exam_rules')) {
            return;
        }
        
        Schema::create('exam_rules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->boolean('is_global')->default(false);
            $table->text('description');
            $table->boolean('is_mandatory')->default(true);
            $table->integer('display_order')->default(0);
            $table->enum('category', ['general', 'conduct', 'materials', 'timing', 'grading', 'other'])->default('general');
            $table->text('penalty_for_violation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it
        if (!Schema::hasTable('exam_rules_created_by_earlier_migration')) {
            Schema::dropIfExists('exam_rules');
        }
    }
};

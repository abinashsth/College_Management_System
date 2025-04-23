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
        Schema::table('class_subjects', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('class_subjects', 'class_id')) {
                $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('class_subjects', 'subject_id')) {
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('class_subjects', 'semester')) {
                $table->integer('semester')->nullable();
            }
            
            if (!Schema::hasColumn('class_subjects', 'year')) {
                $table->integer('year')->nullable();
            }
            
            if (!Schema::hasColumn('class_subjects', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            
            if (!Schema::hasColumn('class_subjects', 'is_core')) {
                $table->boolean('is_core')->default(true);
            }
            
            if (!Schema::hasColumn('class_subjects', 'notes')) {
                $table->text('notes')->nullable();
            }
            
            // Add unique constraint if it doesn't exist
            try {
                $table->unique(['class_id', 'subject_id', 'semester', 'year'], 'class_subject_unique');
            } catch (\Exception $e) {
                // Index might already exist, so we can ignore this error
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_subjects', function (Blueprint $table) {
            // Remove columns (only if we need to roll back)
            // We don't typically remove columns in a down migration that adds columns
        });
    }
};

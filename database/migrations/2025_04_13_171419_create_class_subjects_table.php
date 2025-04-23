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
        // Check if table exists
        if (!Schema::hasTable('class_subjects')) {
            // Create the table if it doesn't exist
            Schema::create('class_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->integer('semester')->nullable();
                $table->integer('year')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_core')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                // Ensure a subject is only added once to a class per semester and year
                $table->unique(['class_id', 'subject_id', 'semester', 'year']);
            });
        } else {
            // Just add the missing columns to the existing table
            Schema::table('class_subjects', function (Blueprint $table) {
                // Only add columns that don't exist yet
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
                
                // Skip adding the unique constraint - it must already exist
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if this migration created the table
        if (!Schema::hasTable('class_subjects_created_by_earlier_migration')) {
            Schema::dropIfExists('class_subjects');
        } else {
            // Otherwise just drop the columns we added
            Schema::table('class_subjects', function (Blueprint $table) {
                // We don't need to drop constraints as we didn't add any
                
                // Remove columns
                $columns = [];
                
                if (Schema::hasColumn('class_subjects', 'class_id')) {
                    $columns[] = 'class_id';
                }
                
                if (Schema::hasColumn('class_subjects', 'subject_id')) {
                    $columns[] = 'subject_id';
                }
                
                if (Schema::hasColumn('class_subjects', 'semester')) {
                    $columns[] = 'semester';
                }
                
                if (Schema::hasColumn('class_subjects', 'year')) {
                    $columns[] = 'year';
                }
                
                if (Schema::hasColumn('class_subjects', 'is_active')) {
                    $columns[] = 'is_active';
                }
                
                if (Schema::hasColumn('class_subjects', 'is_core')) {
                    $columns[] = 'is_core';
                }
                
                if (Schema::hasColumn('class_subjects', 'notes')) {
                    $columns[] = 'notes';
                }
                
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};

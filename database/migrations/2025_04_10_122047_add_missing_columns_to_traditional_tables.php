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
        // Add missing columns to departments table
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                if (!Schema::hasColumn('departments', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
                if (!Schema::hasColumn('departments', 'faculty_id')) {
                    $table->unsignedBigInteger('faculty_id')->nullable()->after('description');
                    // Add foreign key constraint if faculties table exists
                    if (Schema::hasTable('faculties')) {
                        $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('set null');
                    }
                }
            });
        }
        
        // Add missing columns to programs table
        if (Schema::hasTable('programs')) {
            Schema::table('programs', function (Blueprint $table) {
                if (!Schema::hasColumn('programs', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
                if (!Schema::hasColumn('programs', 'department_id')) {
                    $table->unsignedBigInteger('department_id')->nullable()->after('description');
                    // Add foreign key constraint if departments table exists
                    if (Schema::hasTable('departments')) {
                        $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
                    }
                }
                if (!Schema::hasColumn('programs', 'duration')) {
                    $table->integer('duration')->default(4)->after('department_id');
                }
                if (!Schema::hasColumn('programs', 'duration_unit')) {
                    $table->string('duration_unit')->default('years')->after('duration');
                }
                if (!Schema::hasColumn('programs', 'credit_hours')) {
                    $table->integer('credit_hours')->default(120)->after('duration_unit');
                }
                if (!Schema::hasColumn('programs', 'degree_level')) {
                    $table->string('degree_level')->default('Bachelor')->after('credit_hours');
                }
            });
        }
        
        // Add missing columns to faculties table
        if (Schema::hasTable('faculties')) {
            Schema::table('faculties', function (Blueprint $table) {
                if (!Schema::hasColumn('faculties', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We will not remove columns since it might break existing functionality
    }
};

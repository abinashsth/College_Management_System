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
        // Skip if the table doesn't exist
        if (!Schema::hasTable('student_records')) {
            return;
        }
        
        Schema::table('student_records', function (Blueprint $table) {
            // Check if column already exists
            if (!Schema::hasColumn('student_records', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip if the table doesn't exist
        if (!Schema::hasTable('student_records')) {
            return;
        }
        
        Schema::table('student_records', function (Blueprint $table) {
            if (Schema::hasColumn('student_records', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
}; 
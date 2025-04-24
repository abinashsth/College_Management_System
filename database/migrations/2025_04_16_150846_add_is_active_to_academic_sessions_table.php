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
        if (!Schema::hasTable('academic_sessions')) {
            return;
        }
        
        // Skip if the column already exists
        if (Schema::hasColumn('academic_sessions', 'is_active')) {
            return;
        }
        
        Schema::table('academic_sessions', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip if the table doesn't exist
        if (!Schema::hasTable('academic_sessions')) {
            return;
        }
        
        // Skip if the column doesn't exist
        if (!Schema::hasColumn('academic_sessions', 'is_active')) {
            return;
        }
        
        Schema::table('academic_sessions', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};

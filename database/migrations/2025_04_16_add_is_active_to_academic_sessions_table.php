<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First check if the table exists
        if (!Schema::hasTable('academic_sessions')) {
            return;
        }
        
        // First add the is_active column if it doesn't exist
        if (!Schema::hasColumn('academic_sessions', 'is_active')) {
            Schema::table('academic_sessions', function (Blueprint $table) {
                $table->boolean('is_active')->default(false)->after('is_current');
            });
        }
        
        // Now safely update the data
        if (Schema::hasColumn('academic_sessions', 'is_active') && 
            Schema::hasColumn('academic_sessions', 'is_current')) {
            DB::statement('UPDATE academic_sessions SET is_active = is_current');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('academic_sessions') && 
            Schema::hasColumn('academic_sessions', 'is_active')) {
            Schema::table('academic_sessions', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
}; 
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
        Schema::table('courses', function (Blueprint $table) {
            // First add the new column
            if (!Schema::hasColumn('courses', 'status')) {
                $table->boolean('status')->default(true)->after('description');
            }
        });

        // Copy data from old column to new column
        if (Schema::hasColumn('courses', 'is_active')) {
            DB::statement('UPDATE courses SET status = is_active');
            
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // First add back the old column
            if (!Schema::hasColumn('courses', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });

        // Copy data back from new column to old column
        if (Schema::hasColumn('courses', 'status')) {
            DB::statement('UPDATE courses SET is_active = status');
            
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}; 
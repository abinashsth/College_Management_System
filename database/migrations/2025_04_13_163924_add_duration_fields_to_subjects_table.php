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
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'duration_type')) {
                $table->string('duration_type')->default('semester')->after('level');
            }
            
            if (!Schema::hasColumn('subjects', 'year')) {
                $table->string('year')->nullable()->after('semester_offered');
            }
            
            // Add elective field if it doesn't exist
            if (!Schema::hasColumn('subjects', 'elective')) {
                $table->boolean('elective')->default(false)->after('year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'duration_type')) {
                $table->dropColumn('duration_type');
            }
            
            if (Schema::hasColumn('subjects', 'year')) {
                $table->dropColumn('year');
            }
            
            if (Schema::hasColumn('subjects', 'elective')) {
                $table->dropColumn('elective');
            }
        });
    }
}; 
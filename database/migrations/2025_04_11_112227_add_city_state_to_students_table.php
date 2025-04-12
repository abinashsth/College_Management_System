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
        Schema::table('students', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('students', 'city')) {
                $table->string('city')->nullable()->after('student_address');
            }
            
            if (!Schema::hasColumn('students', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Only attempt to drop columns if they exist
            if (Schema::hasColumn('students', 'city')) {
                $table->dropColumn('city');
            }
            
            if (Schema::hasColumn('students', 'state')) {
                $table->dropColumn('state');
            }
        });
    }
};

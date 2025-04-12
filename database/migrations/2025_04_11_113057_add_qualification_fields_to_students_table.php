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
            if (!Schema::hasColumn('students', 'last_qualification')) {
                $table->string('last_qualification')->nullable()->after('previous_education');
            }
            
            if (!Schema::hasColumn('students', 'last_qualification_marks')) {
                $table->string('last_qualification_marks')->nullable()->after('last_qualification');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'last_qualification')) {
                $table->dropColumn('last_qualification');
            }
            
            if (Schema::hasColumn('students', 'last_qualification_marks')) {
                $table->dropColumn('last_qualification_marks');
            }
        });
    }
};

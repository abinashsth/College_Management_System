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
        Schema::table('faculties', function (Blueprint $table) {
            // Add type column with default value 'faculty'
            if (!Schema::hasColumn('faculties', 'type')) {
                $table->string('type')->default('faculty')->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            if (Schema::hasColumn('faculties', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
}; 
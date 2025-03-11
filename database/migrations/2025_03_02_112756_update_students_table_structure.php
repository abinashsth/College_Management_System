<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTableStructure extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Check if the column exists before dropping it
            if (Schema::hasColumn('students', 'roll_number')) {
                $table->dropColumn('roll_number');
            }
            if (Schema::hasColumn('students', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('students', 'contact')) {
                $table->dropColumn('contact');
            }
            if (Schema::hasColumn('students', 'admission_number')) {
                $table->dropColumn('admission_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If you need to restore the columns during rollback
        Schema::table('students', function (Blueprint $table) {
            $table->string('roll_number')->nullable(); // Adjust the type if necessary
            $table->string('name')->nullable();        // Adjust the type if necessary
            $table->string('contact')->nullable();     // Adjust the type if necessary
            $table->string('admission_number')->nullable(); // Adjust the type if necessary
        });
    }
}

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
        Schema::table('exam_results', function (Blueprint $table) {
            // Check if marks_obtained column exists
            if (Schema::hasColumn('exam_results', 'marks_obtained')) {
                // Rename marks_obtained to total_marks if it exists
                $table->renameColumn('marks_obtained', 'total_marks');
            } else if (!Schema::hasColumn('exam_results', 'total_marks')) {
                // Add total_marks if neither column exists
                $table->decimal('total_marks', 5, 2)->nullable();
            }
            
            // Add theory and practical marks columns if they don't exist
            if (!Schema::hasColumn('exam_results', 'theory_marks')) {
                $table->decimal('theory_marks', 5, 2)->nullable();
            }
            
            if (!Schema::hasColumn('exam_results', 'practical_marks')) {
                $table->decimal('practical_marks', 5, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            // We don't want to remove the total_marks column as it might have been renamed
            // Only drop the new columns we added
            if (Schema::hasColumn('exam_results', 'theory_marks')) {
                $table->dropColumn('theory_marks');
            }
            
            if (Schema::hasColumn('exam_results', 'practical_marks')) {
                $table->dropColumn('practical_marks');
            }
        });
    }
};

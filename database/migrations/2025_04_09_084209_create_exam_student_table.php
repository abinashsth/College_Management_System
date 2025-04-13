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
        // Skip if table already exists
        if (Schema::hasTable('exam_student')) {
            return;
        }
        
        Schema::create('exam_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('grade', 8, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->datetime('evaluated_at')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['exam_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it
        if (!Schema::hasTable('exam_student_created_by_earlier_migration')) {
            Schema::dropIfExists('exam_student');
        }
    }
};

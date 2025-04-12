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
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->decimal('marks_obtained', 8, 2)->unsigned()->nullable();
            $table->decimal('total_marks', 8, 2)->unsigned();
            $table->string('grade', 10)->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified', 'published'])->default('draft');
            $table->boolean('is_absent')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Ensure a student can only have one mark entry per exam per subject
            $table->unique(['exam_id', 'student_id', 'subject_id']);
        });

        Schema::create('mark_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mark_id')->constrained()->onDelete('cascade');
            $table->string('component_name');
            $table->decimal('marks_obtained', 8, 2)->unsigned()->nullable();
            $table->decimal('total_marks', 8, 2)->unsigned();
            $table->decimal('weight_percentage', 5, 2)->unsigned()->default(100.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mark_components');
        Schema::dropIfExists('marks');
    }
}; 
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
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('subject_code')->unique();
                $table->string('subject_name');
                $table->text('description')->nullable();
                $table->integer('credit_hours');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->foreignId('faculty_id')->constrained()->onDelete('cascade');
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                // Composite unique key for subject_name within a course
                $table->unique(['subject_name', 'course_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
}; 
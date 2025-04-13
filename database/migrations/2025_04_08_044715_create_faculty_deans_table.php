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
        Schema::create('faculty_deans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('academic_structures')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('appointment_date');
            $table->date('end_date')->nullable();
            $table->string('appointment_reference')->nullable();
            $table->text('job_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure a user can only be dean of one faculty at a time
            $table->unique(['user_id', 'is_active'], 'user_active_dean_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_deans');
    }
};

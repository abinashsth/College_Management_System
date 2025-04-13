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
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('type'); // semester, term, etc.
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->date('registration_start_date')->nullable();
            $table->date('registration_end_date')->nullable();
            $table->date('class_start_date')->nullable();
            $table->date('class_end_date')->nullable();
            $table->date('exam_start_date')->nullable();
            $table->date('exam_end_date')->nullable();
            $table->date('result_date')->nullable();
            $table->timestamps();

            // Ensure unique combination of name and academic year
            $table->unique(['name', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_sessions');
    }
};

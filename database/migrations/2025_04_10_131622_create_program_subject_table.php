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
        Schema::create('program_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('semester')->nullable();
            $table->integer('year')->nullable();
            $table->boolean('is_core')->default(true);
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure a subject can only be added to a program once
            $table->unique(['program_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_subject');
    }
};

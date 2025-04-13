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
        Schema::create('faculty_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('academic_structures')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->boolean('all_day')->default(false);
            $table->string('type')->default('general'); // general, meeting, exam, etc.
            $table->string('status')->default('scheduled'); // scheduled, cancelled, completed
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_events');
    }
};

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
        if (Schema::hasTable('exam_schedules')) {
            return;
        }
        
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('section_id');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location')->nullable();
            $table->string('room_number')->nullable();
            $table->integer('seating_capacity')->nullable();
            $table->boolean('is_rescheduled')->default(false);
            $table->text('reschedule_reason')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Prevent duplicate schedules for the same exam and section
            $table->unique(['exam_id', 'section_id'], 'exam_section_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it
        if (!Schema::hasTable('exam_schedules_created_by_earlier_migration')) {
            Schema::dropIfExists('exam_schedules');
        }
    }
};

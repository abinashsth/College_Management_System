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
        if (Schema::hasTable('exam_supervisors')) {
            return;
        }
        
        Schema::create('exam_supervisors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_schedule_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['chief_supervisor', 'supervisor', 'assistant_supervisor', 'invigilator', 'other'])->default('supervisor');
            $table->time('reporting_time')->nullable();
            $table->time('leaving_time')->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->timestamp('confirmation_time')->nullable();
            $table->boolean('is_attended')->default(false);
            $table->text('responsibilities')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamps();
            
            // Prevent duplicate assignments for the same supervisor and schedule
            $table->unique(['exam_schedule_id', 'user_id'], 'schedule_supervisor_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it
        if (!Schema::hasTable('exam_supervisors_created_by_earlier_migration')) {
            Schema::dropIfExists('exam_supervisors');
        }
    }
};

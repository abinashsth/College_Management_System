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
        Schema::create('classroom_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('room_number');
            $table->integer('floor')->nullable();
            $table->string('building')->nullable();
            $table->integer('capacity')->default(30);
            $table->enum('type', ['lecture', 'lab', 'seminar', 'other'])->default('lecture');
            $table->enum('status', ['available', 'maintenance', 'reserved'])->default('available');
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_allocations');
    }
};

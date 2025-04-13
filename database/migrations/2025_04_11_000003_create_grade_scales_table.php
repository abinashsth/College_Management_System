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
        // Check if table already exists to prevent errors
        if (!Schema::hasTable('grade_scales')) {
            Schema::create('grade_scales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('grade_system_id')->constrained()->onDelete('cascade');
                $table->string('grade');
                $table->string('description')->nullable();
                $table->decimal('min_percentage', 5, 2);
                $table->decimal('max_percentage', 5, 2);
                $table->decimal('grade_point', 5, 2)->nullable();
                $table->boolean('is_fail')->default(false);
                $table->text('remarks')->nullable();
                $table->timestamps();
                
                // Add unique index for grade system and grade with a shorter name
                $table->unique(['grade_system_id', 'grade'], 'grade_scales_sys_grade_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
}; 
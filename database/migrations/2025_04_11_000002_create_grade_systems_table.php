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
        // Check if grade_systems table already exists
        if (!Schema::hasTable('grade_systems')) {
            Schema::create('grade_systems', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->decimal('max_gpa', 4, 2)->default(4.00);
                $table->decimal('pass_percentage', 5, 2)->default(40.00);
                $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_systems');
    }
}; 
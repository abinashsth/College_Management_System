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
        // Skip if table already exists (it's created by an earlier migration)
        if (Schema::hasTable('academic_structures')) {
            return;
        }
        
        Schema::create('academic_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // faculty, department, program
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('academic_structures')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it (not if it was created by earlier migration)
        if (!Schema::hasTable('academic_structures_created_by_earlier_migration')) {
            Schema::dropIfExists('academic_structures');
        }
    }
};

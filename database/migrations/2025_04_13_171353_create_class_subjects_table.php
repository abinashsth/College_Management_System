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
        if (Schema::hasTable('class_subjects')) {
            return;
        }
        
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop if we didn't create it (it was created by earlier migration)
        if (!Schema::hasTable('class_subjects_created_by_earlier_migration')) {
            Schema::dropIfExists('class_subjects');
        }
    }
};

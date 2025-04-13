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
        Schema::create('subject_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('prerequisite_id')->constrained('subjects')->onDelete('cascade');
            $table->string('type')->default('required'); // required, recommended, optional
            $table->string('min_grade')->nullable(); // Minimum grade required in the prerequisite
            $table->text('description')->nullable(); // Description of why this is a prerequisite
            $table->string('status')->default('active');
            $table->timestamps();
            
            // Ensure a prerequisite is only added once per subject
            $table->unique(['subject_id', 'prerequisite_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_prerequisites');
    }
};

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
        // Check if programs table already exists
        if (!Schema::hasTable('programs')) {
            Schema::create('programs', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('code');
                $table->text('description')->nullable();
                $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
                $table->foreignId('coordinator_id')->nullable()->constrained('users')->onDelete('set null');
                $table->integer('duration')->default(4);
                $table->string('duration_unit')->default('years');
                $table->integer('credit_hours')->nullable();
                $table->string('degree_level')->nullable();
                $table->text('admission_requirements')->nullable();
                $table->text('curriculum')->nullable();
                $table->decimal('tuition_fee', 10, 2)->nullable();
                $table->integer('max_students')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('type')->default('program');
                $table->boolean('status')->default(true);
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};

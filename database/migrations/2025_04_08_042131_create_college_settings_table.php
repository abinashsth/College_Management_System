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
        Schema::create('college_settings', function (Blueprint $table) {
            $table->id();
            $table->string('college_name');
            $table->string('college_code');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->year('established_year')->nullable();
            $table->text('accreditation_info')->nullable();
            $table->date('academic_year_start')->nullable();
            $table->date('academic_year_end')->nullable();
            $table->string('grading_system')->nullable();
            $table->string('principal_name')->nullable();
            $table->text('vision_statement')->nullable();
            $table->text('mission_statement')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_settings');
    }
};

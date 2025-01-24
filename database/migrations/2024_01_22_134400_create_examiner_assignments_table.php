<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('examiner_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Examiner's user ID
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Each examiner can be assigned to a subject in a class only once per session
            $table->unique(['user_id', 'class_id', 'subject_id', 'academic_session_id'], 'unique_examiner_assignment');
        });
    }

    public function down()
    {
        Schema::dropIfExists('examiner_assignments');
    }
};

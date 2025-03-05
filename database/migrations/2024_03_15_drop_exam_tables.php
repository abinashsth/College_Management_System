<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop exam-related tables
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('terminal_marks_ledger');
        Schema::dropIfExists('final_grade_sheets');
    }

    public function down()
    {
        // Tables will be recreated by their original migrations if needed
    }
}; 
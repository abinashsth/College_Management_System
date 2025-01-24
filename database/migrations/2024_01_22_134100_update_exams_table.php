<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateExamsTable extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'academic_session_id')) {
                $table->foreignId('academic_session_id')->after('exam_type_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('exams', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('description');
            }
        });

        // Create exam_subject pivot table if it doesn't exist
        if (!Schema::hasTable('exam_subject')) {
            Schema::create('exam_subject', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained()->onDelete('cascade');
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->decimal('theory_marks', 5, 2)->default(50);
                $table->decimal('practical_marks', 5, 2)->default(50);
                $table->decimal('passing_marks', 5, 2)->default(40);
                $table->unique(['exam_id', 'subject_id']);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['academic_session_id', 'is_published']);
        });
        Schema::dropIfExists('exam_subject');
    }
}

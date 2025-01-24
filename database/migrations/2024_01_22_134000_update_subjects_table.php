<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSubjectsTable extends Migration
{
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'total_theory_marks')) {
                $table->decimal('total_theory_marks', 5, 2)->default(50)->after('code');
            }
            if (!Schema::hasColumn('subjects', 'total_practical_marks')) {
                $table->decimal('total_practical_marks', 5, 2)->default(50)->after('total_theory_marks');
            }
            if (!Schema::hasColumn('subjects', 'passing_marks')) {
                $table->decimal('passing_marks', 5, 2)->default(40)->after('total_practical_marks');
            }
        });

        // Create class_subject pivot table if it doesn't exist
        if (!Schema::hasTable('class_subject')) {
            Schema::create('class_subject', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_id')->constrained()->onDelete('cascade');
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
                $table->unique(['class_id', 'subject_id', 'academic_session_id']);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['total_theory_marks', 'total_practical_marks', 'passing_marks']);
        });
        Schema::dropIfExists('class_subject');
    }
}

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
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('subject_code')->unique();
                $table->integer('roll_number');
                $table->string('subject_name');
                $table->text('description')->nullable();
                $table->integer('credit_hours');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->boolean('status')->default(true);
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['subject_name', 'course_id']);
            });
        } else {
            Schema::table('subjects', function (Blueprint $table) {
                if (!Schema::hasColumn('subjects', 'subject_code')) {
                    $table->string('subject_code')->unique()->after('id');
                }
                if (!Schema::hasColumn('subjects', 'subject_name')) {
                    $table->string('subject_name')->after('subject_code');
                }
                if (!Schema::hasColumn('subjects', 'description')) {
                    $table->text('description')->nullable()->after('subject_name');
                }
                if (!Schema::hasColumn('subjects', 'credit_hours')) {
                    $table->integer('credit_hours')->after('description');
                }
                if (!Schema::hasColumn('subjects', 'course_id')) {
                    $table->foreignId('course_id')->constrained()->onDelete('cascade')->after('credit_hours');
                }
                if (!Schema::hasColumn('subjects', 'status')) {
                    $table->boolean('status')->default(true)->after('course_id');
                }
                if (!Schema::hasColumn('subjects', 'created_by')) {
                    $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to reverse these changes to preserve data
    }
}; 
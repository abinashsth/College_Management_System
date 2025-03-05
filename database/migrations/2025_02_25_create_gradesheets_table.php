<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('gradesheets')) {
            Schema::create('gradesheets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('session_id')->constrained('academic_sessions')->onDelete('cascade');
                $table->integer('total_marks');
                $table->float('percentage', 5, 2);
                $table->string('grade');
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        } else {
            // Check if the foreign key exists
            $foreignKeyExists = DB::select("
                SELECT * 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE CONSTRAINT_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'gradesheets' 
                AND CONSTRAINT_NAME = 'gradesheets_session_id_foreign'
            ");
            
            if (!empty($foreignKeyExists)) {
                Schema::table('gradesheets', function (Blueprint $table) {
                    $table->dropForeign(['session_id']);
                });
            }
            
            Schema::table('gradesheets', function (Blueprint $table) {
                if (Schema::hasColumn('gradesheets', 'session_id')) {
                    $table->foreign('session_id')
                        ->references('id')
                        ->on('academic_sessions')
                        ->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gradesheets');
    }
}; 
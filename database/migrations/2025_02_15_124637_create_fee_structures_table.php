<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up() {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('class_name');
            $table->string('academic_year', 10); // Example: "2024-2025"
            $table->decimal('tuition_fee', 10, 2);
            $table->decimal('admission_fee', 10, 2);
            $table->decimal('exam_fee', 10, 2);
            $table->decimal('total_fee', 10, 2);    
            $table->string('status');
            
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('fee_structures');
    }
};

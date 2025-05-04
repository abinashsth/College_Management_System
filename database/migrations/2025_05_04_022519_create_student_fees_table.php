<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentFeesTable extends Migration
{
    public function up()
    {
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('fee_type');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['Paid', 'Unpaid'])->default('Unpaid');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_fees');
    }
}
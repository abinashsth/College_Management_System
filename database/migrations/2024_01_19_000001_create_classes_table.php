<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->integer('capacity')->default(30);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('classes');
    }
}; 
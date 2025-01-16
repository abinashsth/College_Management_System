<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('contact_number');
            $table->date('dob');
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->boolean('status')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
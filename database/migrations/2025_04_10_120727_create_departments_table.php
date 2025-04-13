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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');
            $table->string('logo')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->date('established_date')->nullable();
            $table->string('type')->default('department');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

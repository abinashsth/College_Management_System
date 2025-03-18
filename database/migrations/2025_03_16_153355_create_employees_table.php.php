<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->string('designation');
            $table->date('joining_date');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            // $table->string('avatar')->nullable();
            // $table->text('address');
            // $table->string('city');
            // $table->string('state');
            // $table->string('postal_code');
            // $table->string('country');
            // $table->string('emergency_contact_name');
            // $table->string('emergency_contact_phone');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
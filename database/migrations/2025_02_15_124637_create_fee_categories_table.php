<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



return new class extends Migration {
    public function up() {
        Schema::create('fee_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Example: Tuition, Exam, Hostel
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('fee_categories');
    }
};

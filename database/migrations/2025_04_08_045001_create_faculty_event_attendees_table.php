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
        Schema::create('faculty_event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('invited'); // invited, confirmed, declined, attended
            $table->text('response')->nullable();
            $table->timestamps();
            
            // Prevent duplicate attendees
            $table->unique(['faculty_event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_event_attendees');
    }
};

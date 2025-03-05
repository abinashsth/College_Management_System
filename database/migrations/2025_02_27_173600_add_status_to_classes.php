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
        // Add status column if it doesn't exist
        if (!Schema::hasColumn('classes', 'status')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active');
            });
        }

        // Set default status for existing records
        DB::statement("UPDATE classes SET status = 'active' WHERE status IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('classes', 'status')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}; 
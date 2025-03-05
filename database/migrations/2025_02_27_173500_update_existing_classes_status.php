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
        // First add the status column if it doesn't exist
        if (!Schema::hasColumn('classes', 'status')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('session_id');
            });
        }

        // Update all existing records to have 'active' status
        DB::table('classes')->whereNull('status')->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}; 
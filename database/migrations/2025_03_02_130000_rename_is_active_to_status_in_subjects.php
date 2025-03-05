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
        if (Schema::hasTable('subjects') && Schema::hasColumn('subjects', 'is_active')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->renameColumn('is_active', 'status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('subjects') && Schema::hasColumn('subjects', 'status')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->renameColumn('status', 'is_active');
            });
        }
    }
}; 
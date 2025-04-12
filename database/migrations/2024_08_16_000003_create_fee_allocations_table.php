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
        // Check if table already exists
        if (!Schema::hasTable('fee_allocations')) {
            Schema::create('fee_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fee_type_id')->constrained();
                $table->enum('applicable_to', ['class', 'program', 'section', 'student']);
                $table->unsignedBigInteger('applicable_id')->nullable(); // ID of class, program, section, or student
                $table->decimal('amount', 10, 2)->nullable(); // Override default fee amount if needed
                $table->foreignId('academic_year_id')->constrained();
                $table->date('due_date')->nullable();
                $table->string('academic_term')->nullable(); // For term-specific fees
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                
                // Using a shorter index name to avoid MySQL's name limit
                $table->index(['applicable_to', 'applicable_id', 'academic_year_id'], 'fee_allocation_lookup_index');
            });
        } else {
            // If the table already exists, check for index and add it if missing
            // First, let's see if the index exists by checking the information schema
            $indexExists = DB::select("
                SELECT COUNT(1) as index_exists
                FROM information_schema.statistics
                WHERE table_schema = DATABASE()
                AND table_name = 'fee_allocations'
                AND index_name = 'fee_allocation_lookup_index'
            ");
            
            if (!$indexExists[0]->index_exists) {
                // Add the index with a safe name
                try {
                    DB::statement('ALTER TABLE fee_allocations ADD INDEX fee_allocation_lookup_index (applicable_to, applicable_id, academic_year_id)');
                } catch (\Exception $e) {
                    // If we get an error, it's likely the index already exists with a different name
                    // We can safely ignore this
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_allocations');
    }
}; 
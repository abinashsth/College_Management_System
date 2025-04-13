<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixMigrationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if the migration is already in the migrations table
        $exists = DB::table('migrations')
            ->where('migration', '2025_04_10_120727_create_departments_table')
            ->exists();
            
        if (!$exists) {
            // Add the migration to the migrations table as completed
            DB::table('migrations')->insert([
                'migration' => '2025_04_10_120727_create_departments_table',
                'batch' => 8, // Use batch number higher than the last one
            ]);
            $this->command->info('Migration 2025_04_10_120727_create_departments_table marked as completed.');
        } else {
            $this->command->info('Migration already exists in migrations table.');
        }
    }
} 
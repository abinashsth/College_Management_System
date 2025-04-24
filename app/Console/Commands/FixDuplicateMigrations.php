<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicateMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix duplicate migrations by marking them as completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running migration fix command...');

        try {
            // Get the latest batch number
            $latestBatch = DB::table('migrations')->max('batch');
            
            // List of duplicate migrations to mark as completed
            $migrations = [
                '2023_07_01_000000_create_masks_table',
                '2023_10_20_000000_create_masks_table'
            ];

            foreach ($migrations as $migration) {
                // Check if the migration already exists in the table
                $exists = DB::table('migrations')
                    ->where('migration', $migration)
                    ->exists();
                
                if (!$exists) {
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => $latestBatch + 1
                    ]);
                    $this->info("Marked migration as completed: $migration");
                } else {
                    $this->info("Migration already exists in the table: $migration");
                }
            }

            $this->info('Successfully fixed duplicate migrations!');
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 
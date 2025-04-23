<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpdateSubjectFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subjects:update-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add missing fields to subjects table for better duration handling';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating subjects table with duration fields...');
        
        // Create migration file with timestamp if it doesn't exist
        $migrationPath = database_path('migrations');
        $timestamp = date('Y_m_d_His');
        $migrationFileName = $timestamp . '_add_duration_fields_to_subjects_table.php';
        
        $migrationContents = file_get_contents(database_path('migrations/add_duration_fields_to_subjects_table.php'));
        
        // Save migration with timestamp to migrations directory
        file_put_contents($migrationPath . '/' . $migrationFileName, $migrationContents);
        
        // Run the migration
        $this->info('Running migration...');
        Artisan::call('migrate', ['--force' => true]);
        
        $this->info('Migration output:');
        $this->line(Artisan::output());
        
        // Delete the timestamp-less migration file
        if (file_exists(database_path('migrations/add_duration_fields_to_subjects_table.php'))) {
            unlink(database_path('migrations/add_duration_fields_to_subjects_table.php'));
        }
        
        $this->info('Subject fields have been updated successfully!');
        $this->info('You can now use the duration_type, year, and elective fields.');
        
        return Command::SUCCESS;
    }
} 
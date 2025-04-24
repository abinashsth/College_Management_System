<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Scripts\Database\MigrationFixer;

class FixMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix duplicate migrations and add missing foreign key constraints';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration fixes...');

        $fixer = new MigrationFixer();

        $this->info('Fixing duplicate migrations...');
        $fixer->fixDuplicateMigrations();

        $this->info('Adding missing foreign key constraints...');
        $fixer->addMissingForeignKeys();

        $this->info('Syncing permissions...');
        $fixer->syncPermissions();

        $this->info('Migration fixes completed successfully!');
    }
} 
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\SubjectPermissionsSeeder;

class AddSubjectPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:add-subject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add subject management permissions to the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Adding subject management permissions...');
        
        // Clear permission cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $seeder = new SubjectPermissionsSeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('Subject management permissions have been added successfully!');
        $this->info('You can now access Subject Management in the sidebar.');
        
        return Command::SUCCESS;
    }
} 
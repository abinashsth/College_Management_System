<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RolePermissionUpdate;

class UpdateRolePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-role-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update role permissions for academic dean, examiner, and accountant roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating role permissions...');
        
        // Clear cache for permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Run the seeder
        $seeder = new RolePermissionUpdate();
        $seeder->run();
        
        $this->info('Role permissions updated successfully!');
        $this->info('The following roles have been updated:');
        $this->line('- admin (now includes academic dean permissions)');
        $this->line('- academic-dean (new role with faculty management)');
        $this->line('- examiner (updated with enhanced exam permissions)');
        $this->line('- accountant (updated with financial officer permissions)');
        
        return Command::SUCCESS;
    }
} 
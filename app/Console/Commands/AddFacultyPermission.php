<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddFacultyPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-faculty-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add the manage faculty permission to the admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding manage faculty permission...');
        
        // Clear cache for permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create the permission if it doesn't exist
        $permission = Permission::firstOrCreate([
            'name' => 'manage faculty',
            'guard_name' => 'web'
        ]);
        
        // Get the admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            // Give the permission to the admin role
            if (!$adminRole->hasPermissionTo('manage faculty')) {
                $adminRole->givePermissionTo('manage faculty');
                $this->info('Permission "manage faculty" has been added to admin role.');
            } else {
                $this->info('Admin role already has the "manage faculty" permission.');
            }
        } else {
            $this->error('Admin role not found!');
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
} 
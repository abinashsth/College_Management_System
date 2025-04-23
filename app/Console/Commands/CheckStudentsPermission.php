<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CheckStudentsPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-students-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the manage students permission exists and is assigned to the admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking students permission...');
        
        // Check if the permission exists
        $permission = Permission::where('name', 'manage students')->first();
        
        if ($permission) {
            $this->info('Permission "manage students" exists with ID: ' . $permission->id);
            $this->info('Guard name: ' . $permission->guard_name);
            
            // Get the admin role
            $adminRole = Role::where('name', 'admin')->first();
            
            if ($adminRole) {
                $this->info('Admin role exists with ID: ' . $adminRole->id);
                
                // Check if the permission is assigned to the admin role
                if ($adminRole->hasPermissionTo('manage students')) {
                    $this->info('Admin role has the "manage students" permission.');
                } else {
                    $this->error('Admin role DOES NOT have the "manage students" permission!');
                    
                    // Try to assign it
                    $adminRole->givePermissionTo('manage students');
                    $this->info('Permission has been assigned now!');
                }
            } else {
                $this->error('Admin role not found!');
            }
        } else {
            $this->error('Permission "manage students" DOES NOT exist!');
            
            // Create the permission
            $permission = Permission::create([
                'name' => 'manage students',
                'guard_name' => 'web'
            ]);
            
            $this->info('Permission has been created with ID: ' . $permission->id);
            
            // Get the admin role
            $adminRole = Role::where('name', 'admin')->first();
            
            if ($adminRole) {
                // Assign the permission
                $adminRole->givePermissionTo('manage students');
                $this->info('Permission has been assigned to the admin role.');
            }
        }
        
        return Command::SUCCESS;
    }
} 
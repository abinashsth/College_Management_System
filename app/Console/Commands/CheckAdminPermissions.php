<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CheckAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-admin-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update all permissions for the admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking admin permissions...');
        
        // Clear cache for permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $this->error('Admin role not found!');
            return Command::FAILURE;
        }
        
        $this->info('Admin role found with ID: ' . $adminRole->id);
        
        // Define all expected admin permissions
        $adminPermissions = [
            // Dashboard
            'view dashboard',
            
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permission Management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            
            // Student Management
            'view students',
            'create students',
            'edit students',
            'delete students',
            'manage students',
            
            // Class Management
            'view classes',
            'create classes',
            'edit classes',
            'delete classes',
            'manage classes',
            
            // Exam Management
            'view exams',
            'create exams',
            'edit exams',
            'delete exams',
            'grade exams',
            'manage exams',
            'manage exam schedules',
            'manage exam supervisors',
            'manage exam rules',
            'manage exam materials',
            'publish exam results',
            
            // Mark Management
            'view marks',
            'create marks',
            'edit marks',
            'verify marks',
            'publish marks',
            'delete marks',
            
            // Account Management
            'view accounts',
            'create accounts',
            'edit accounts',
            'delete accounts',
            'manage accounts',
            
            // Settings Management
            'manage settings',
            
            // Activity Logs
            'view activity logs',
            'clear activity logs',
            
            // Subject Management
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',
            'manage subjects',
            'assign teachers to subjects',
            
            // Faculty Management
            'manage faculty',
            
            // Department Management
            'manage departments',
            
            // Section Management
            'view sections',
            'manage sections',
            
            // Report Management
            'view reports',
            'generate reports',
            'export reports',
            
            // Profile
            'view profile',
            'edit profile'
        ];
        
        // Ensure all permissions exist in the database
        $this->info('Ensuring all required permissions exist...');
        foreach ($adminPermissions as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
            
            // Assign to admin role if not already assigned
            if (!$adminRole->hasPermissionTo($permissionName)) {
                $adminRole->givePermissionTo($permissionName);
                $this->info("Added permission: {$permissionName}");
            }
        }
        
        $this->info('All permissions have been verified and assigned to the admin role.');
        
        return Command::SUCCESS;
    }
} 
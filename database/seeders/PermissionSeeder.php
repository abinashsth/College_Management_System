<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
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
            
            // Class Management
            'view classes',
            'create classes',
            'edit classes',
            'delete classes',
            
            // Exam Management
            'view exams',
            'create exams',
            'edit exams',
            'delete exams',
            'grade exams',
            
            // Exam Session Management
            'manage exam sessions',
            'view exam sessions',
            'create exam sessions',
            'edit exam sessions',
            'delete exam sessions',
            
            // Mark Management
            'enter marks',
            'edit marks',
            'delete marks',
            'view marks',
            
            // Account Management
            'view accounts',
            'create accounts',
            'edit accounts',
            'delete accounts',
            'manage accounts',
            
            // Profile
            'view profile',
            'edit profile'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Exam Management Permissions
        $examPermissions = [
            'view exams',
            'create exams',
            'edit exams',
            'delete exams',
            'manage exams',
            'grade exams',
            'view marks',
            'edit marks',
            'delete marks',
            'enter marks',
            'manage exam sessions',
            'view exam sessions',
            'create exam sessions',
            'edit exam sessions',
            'delete exam sessions',
        ];

        foreach ($examPermissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Assign permissions to super-admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());
    }
} 
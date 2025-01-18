<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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
            
            // Account Management
            'view accounts',
            'create accounts',
            'edit accounts',
            'delete accounts',
            
            // Profile
            'view profile',
            'edit profile'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
} 
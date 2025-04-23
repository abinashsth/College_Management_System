<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Super Admin gets all permissions
        $superAdmin = Role::where('name', 'super-admin')->first();
        
        // Ensure mask-related permissions exist and are assigned to SuperAdmin
        $maskPermissions = [
            'view marks',
            'create marks',
            'edit marks',
            'verify marks',
            'publish marks',
            'delete marks'
        ];
        
        // Ensure these permissions exist
        foreach ($maskPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        $superAdmin->givePermissionTo(Permission::all());

        // Admin permissions
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo([
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
        ]);

        // Teacher permissions
        $teacher = Role::where('name', 'teacher')->first();
        $teacher->givePermissionTo([
            'view dashboard',
            'view students',
            'view classes',
            'view exams', 'create exams', 'edit exams', 'grade exams',
            'view marks', 'create marks', 'edit marks',
            'view profile', 'edit profile'
        ]);

        // Student permissions
        $student = Role::where('name', 'student')->first();
        $student->givePermissionTo([
            'view dashboard',
            'view exams',
            'view marks',
            'view profile', 'edit profile'
        ]);
    }
} 
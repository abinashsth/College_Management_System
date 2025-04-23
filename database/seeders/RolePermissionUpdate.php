<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolePermissionUpdate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Ensure critical permissions exist - create them directly
        $criticalPermissions = [
            'manage faculty',
            'manage departments',
            'manage programs',
            'view students'
        ];
        
        // Create critical permissions explicitly 
        foreach ($criticalPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        
        // Create or update Academic Dean role
        $academicDeanRole = Role::firstOrCreate(['name' => 'academic-dean']);
        
        // Academic Dean Permissions
        $academicDeanPermissions = [
            // Dashboard access
            'view dashboard',
            
            // Faculty Management
            'manage faculty',
            'view faculty',
            'create faculty',
            'edit faculty',
            'delete faculty',
            
            // Department Management
            'manage departments',
            'view departments',
            'create departments',
            'edit departments',
            
            // Program Management
            'manage programs',
            'view programs',
            'create programs',
            'edit programs',
            
            // Student Management
            'view students',
            'edit students',
            
            // Class Management
            'view classes',
            'create classes',
            'edit classes',
            
            // Section Management
            'view sections',
            'create sections',
            'edit sections',
            
            // Classroom Allocation
            'view classroom allocations',
            'create classroom allocations',
            
            // Exam Management
            'view exams',
            'create exams',
            'edit exams',
            'grade exams',
            
            // Profile
            'view profile',
            'edit profile'
        ];
        
        // Ensure all permissions exist
        foreach ($academicDeanPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        
        // Assign permissions to Academic Dean role
        $academicDeanRole->syncPermissions($academicDeanPermissions);
        
        // Update Examiner role with enhanced permissions
        $examinerRole = Role::firstOrCreate(['name' => 'examiner']);
        
        // Examiner Permissions
        $examinerPermissions = [
            // Dashboard access
            'view dashboard',
            
            // Exam Management
            'view exams',
            'create exams',
            'edit exams',
            'delete exams',
            'grade exams',
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
            
            // Student access (view only)
            'view students',
            
            // Result Management
            'process results',
            'verify results',
            'publish results',
            
            // Profile
            'view profile',
            'edit profile'
        ];
        
        // Ensure all permissions exist
        foreach ($examinerPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        
        // Assign permissions to Examiner role
        $examinerRole->syncPermissions($examinerPermissions);
        
        // Update Accountant role with financial officer permissions
        $accountantRole = Role::firstOrCreate(['name' => 'accountant']);
        
        // Accountant/Financial Officer Permissions
        $accountantPermissions = [
            // Dashboard access
            'view dashboard',
            
            // Finance Management
            'view finances',
            'manage finances',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'create payments',
            'void payments',
            'manage fee structure',
            'manage scholarships',
            'generate finance reports',
            
            // Student limited access (for financial records)
            'view students',
            
            // Account Management
            'view accounts',
            'create accounts',
            'edit accounts',
            'delete accounts',
            
            // Profile
            'view profile',
            'edit profile'
        ];
        
        // Ensure all permissions exist
        foreach ($accountantPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        
        // Assign permissions to Accountant role
        $accountantRole->syncPermissions($accountantPermissions);
        
        // Update Admin role to include academic dean permissions
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminPermissions = array_merge(
                $academicDeanPermissions,
                [
                    // Admin-specific permissions
                    'view roles', 
                    'create roles', 
                    'edit roles', 
                    'delete roles',
                    'view permissions', 
                    'create permissions', 
                    'edit permissions', 
                    'delete permissions',
                    'manage settings',
                    'view activity logs',
                    'clear activity logs'
                ]
            );
            
            $adminRole->syncPermissions($adminPermissions);
        }
    }
} 
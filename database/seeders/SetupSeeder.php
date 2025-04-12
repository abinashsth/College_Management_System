<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use DB;

class SetupSeeder extends Seeder
{
    public function run()
    {
        // Clear cache and truncate tables
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create permissions
        $permissions = [
            // Dashboard
            'view dashboard',
            // User Management
            'view users', 'create users', 'edit users', 'delete users',
            // Role Management
            'view roles', 'create roles', 'edit roles', 'delete roles',
            // Permission Management
            'view permissions', 'create permissions', 'edit permissions', 'delete permissions',
            // Student Management
            'view students', 'create students', 'edit students', 'delete students',
            // Class Management
            'view classes', 'create classes', 'edit classes', 'delete classes',
            // Exam Management
            'view exams', 'create exams', 'edit exams', 'delete exams', 'grade exams',
            // Account Management
            'view accounts', 'create accounts', 'edit accounts', 'delete accounts',
            // Profile
            'view profile', 'edit profile'
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $roleNames = ['super-admin', 'admin', 'accountant', 'examiner', 'teacher', 'student'];
        foreach ($roleNames as $roleName) {
            Role::create(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Define role permissions
        $rolePermissions = [
            'admin' => [
                'view dashboard',
                'view users', 'create users', 'edit users', 'delete users',
                'view roles', 'create roles', 'edit roles', 'delete roles',
                'view permissions', 'create permissions', 'edit permissions', 'delete permissions',
                'view students', 'create students', 'edit students', 'delete students',
                'view classes', 'create classes', 'edit classes', 'delete classes',
                'view exams', 'create exams', 'edit exams', 'delete exams', 'grade exams',
                'view accounts', 'create accounts', 'edit accounts', 'delete accounts',
                'view profile', 'edit profile'
            ],
            'accountant' => [
                'view dashboard',
                'view accounts', 'create accounts', 'edit accounts',
                'view profile', 'edit profile'
            ],
            'examiner' => [
                'view dashboard',
                'view exams', 'create exams', 'edit exams', 'grade exams',
                'view students',
                'view profile', 'edit profile'
            ],
            'teacher' => [
                'view dashboard',
                'view students',
                'view classes',
                'view exams', 'grade exams',
                'view profile', 'edit profile'
            ],
            'student' => [
                'view dashboard',
                'view exams',
                'view profile', 'edit profile'
            ]
        ];

        // Assign permissions to roles
        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();
            $permissions = Permission::whereIn('name', $permissionNames)->get();
            $role->syncPermissions($permissions);
        }

        // Give all permissions to super-admin
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $superAdminRole->syncPermissions(Permission::all());

        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        // Assign super-admin role
        $superAdmin->roles()->detach();
        $superAdmin->assignRole('super-admin');
    }
} 
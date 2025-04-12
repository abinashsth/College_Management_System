<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables to avoid duplicate entries
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Define permissions
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

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define roles and their permissions
        $roles = [
            'super-admin' => Permission::all(),
            'admin' => [
                'view dashboard',
                'view users', 'create users', 'edit users',
                'view roles', 'create roles', 'edit roles',
                'view students', 'create students', 'edit students',
                'view classes', 'create classes', 'edit classes',
                'view exams', 'create exams', 'edit exams',
                'view accounts',
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
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        // Create users and assign roles
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'super-admin'
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@example.com',
                'password' => Hash::make('password123'),
                'role' => 'teacher'
            ],
            [
                'name' => 'Student User',
                'email' => 'student@example.com',
                'password' => Hash::make('password123'),
                'role' => 'student'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email']
            ], [
                'name' => $userData['name'],
                'password' => $userData['password']
            ]);

            $user->assignRole($userData['role']);
        }
    }
}

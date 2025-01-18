<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create super-admin role first
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        // Give super-admin ALL permissions
        $superAdminRole->givePermissionTo(Permission::all());

        // Define roles and their permissions
        $roles = [
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
            'teacher' => [
                'view dashboard',
                'view students',
                'view classes',
                'view exams', 'grade exams',
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
            'student' => [
                'view dashboard',
                'view exams',
                'view profile', 'edit profile'
            ]
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $permissionObjects = Permission::whereIn('name', $permissions)->get();
            $role->syncPermissions($permissionObjects);
        }
    }
} 
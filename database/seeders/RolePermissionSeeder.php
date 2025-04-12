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
        $superAdmin->givePermissionTo(Permission::all());

        // Admin permissions
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo([
            'view dashboard',
            'view students', 'create students', 'edit students',
            'view classes', 'create classes', 'edit classes',
            'view exams', 'create exams', 'edit exams',
            'view accounts', 'create accounts', 'edit accounts',
            'manage settings',
            'view profile', 'edit profile'
        ]);

        // Teacher permissions
        $teacher = Role::where('name', 'teacher')->first();
        $teacher->givePermissionTo([
            'view dashboard',
            'view students',
            'view classes',
            'view exams', 'create exams', 'edit exams', 'grade exams',
            'view profile', 'edit profile'
        ]);

        // Student permissions
        $student = Role::where('name', 'student')->first();
        $student->givePermissionTo([
            'view dashboard',
            'view exams',
            'view profile', 'edit profile'
        ]);
    }
} 
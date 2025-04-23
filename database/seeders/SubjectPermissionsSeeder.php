<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SubjectPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Subject management permissions
        $permissions = [
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',
            'manage subjects',
            'assign teachers to subjects',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to super-admin role
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }

        // Assign permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Assign view permissions to teacher role
        $teacherRole = Role::where('name', 'teacher')->first();
        if ($teacherRole) {
            $teacherRole->givePermissionTo(['view subjects']);
        }

        $this->command->info('Subject management permissions created and assigned successfully.');
    }
} 
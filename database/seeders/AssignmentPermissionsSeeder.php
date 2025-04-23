<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignmentPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Assignment Permissions
        $assignmentPermissions = [
            'view assignments',
            'create assignments',
            'edit assignments',
            'delete assignments',
            'grade assignments',
            'submit assignments',
        ];

        foreach ($assignmentPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::findByName('Admin');
        $teacherRole = Role::findByName('Teacher');
        $studentRole = Role::findByName('Student');

        if ($adminRole) {
            $adminRole->givePermissionTo($assignmentPermissions);
        }

        if ($teacherRole) {
            $teacherRole->givePermissionTo([
                'view assignments',
                'create assignments',
                'edit assignments',
                'grade assignments',
            ]);
        }

        if ($studentRole) {
            $studentRole->givePermissionTo([
                'view assignments',
                'submit assignments',
            ]);
        }
    }
}

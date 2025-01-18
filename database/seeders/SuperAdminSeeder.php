<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);

        // Get all permissions and sync them to super-admin role
        $permissions = Permission::all();
        $superAdminRole->syncPermissions($permissions);

        // Create super admin user if it doesn't exist
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        // Sync the super-admin role to the user
        $superAdmin->syncRoles('super-admin');
    }
} 
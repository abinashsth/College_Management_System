<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FinancePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create finance-related permissions
        $permissions = [
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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $admin = Role::where('name', 'Admin')->first();
        $accountant = Role::where('name', 'Accountant')->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        if ($accountant) {
            $accountant->givePermissionTo([
                'view finances',
                'create invoices',
                'edit invoices',
                'create payments',
                'generate finance reports',
            ]);
        }
    }
} 
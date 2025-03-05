<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache before seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->call([
            PermissionSeeder::class,    // First create permissions
            RoleSeeder::class,          // Then create roles
            AdminSeeder::class          // Finally create admin users
        ]);
    }
}
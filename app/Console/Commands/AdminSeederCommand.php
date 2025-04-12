<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\AdminSeeder;

class AdminSeederCommand extends Command
{
    protected $signature = 'db:seed-admin';

    protected $description = 'Seed admin users into the database';

    public function handle()
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Run the seeder
        $seeder = new AdminSeeder();
        $seeder->run();

        $this->info('Admin users seeded successfully!');
        $this->info('Credentials:');
        $this->info('Super Admin - Email: superadmin@admin.com, Password: superadmin123');
        $this->info('Admin - Email: admin@admin.com, Password: admin123');
        $this->info('Teacher - Email: teacher@admin.com, Password: teacher123');
    }
} 
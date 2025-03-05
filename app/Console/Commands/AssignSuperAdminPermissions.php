<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignSuperAdminPermissions extends Command
{
    protected $signature = 'admin:assign-permissions {email : The email of the super admin user}';
    protected $description = 'Assign all permissions to a super admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        // Create or get super-admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Assign all permissions to super-admin role
        $permissions = Permission::all();
        $superAdminRole->syncPermissions($permissions);

        // Assign super-admin role to user
        $user->assignRole($superAdminRole);

        $this->info("Successfully assigned all permissions to user {$email}");
        return 0;
    }
} 
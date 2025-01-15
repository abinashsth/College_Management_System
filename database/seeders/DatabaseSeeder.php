<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Define roles and descriptions
        $rolesData = [
            ['name' => 'Admin', 'description' => 'Full access to all features'],
            ['name' => 'Super Admin', 'description' => 'Full access to all features and management'],
            ['name' => 'Accountant', 'description' => 'Limited access to Account module'],
            ['name' => 'Examiner', 'description' => 'Limited access to Exam module'],
            ['name' => 'Teacher', 'description' => 'Manage classes and view student profiles'],
            ['name' => 'Student', 'description' => 'View own profile and enrolled courses'],
        ];

        // Create roles
        foreach ($rolesData as $roleData) {
            $role = Role::firstOrCreate(['name' => $roleData['name']], ['description' => $roleData['description']]);
        }

        // Define users and their roles
        $usersData = [
            ['name' => 'Super Admin User', 'email' => 'superadmin@example.com', 'role' => 'Super Admin'],
            ['name' => 'Admin User', 'email' => 'admin@example.com', 'role' => 'Admin'],
            ['name' => 'Accountant User', 'email' => 'accountant@example.com', 'role' => 'Accountant'],
            ['name' => 'Examiner User', 'email' => 'examiner@example.com', 'role' => 'Examiner'],
            ['name' => 'Teacher User', 'email' => 'teacher@example.com', 'role' => 'Teacher'],
            ['name' => 'Student User', 'email' => 'student@example.com', 'role' => 'Student'],
        ];

        // Create users and assign roles dynamically
        foreach ($usersData as $userData) {
            $user = User::factory()->create([
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]);
            $role = Role::where('name', $userData['role'])->first();
            $user->roles()->attach($role);
        }

        // Seed permissions and attach to roles (for Admin role in this case)
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $adminRole = Role::where('name', 'Admin')->first();
            $adminRole->permissions()->attach($permission->id);
        }
    }
}

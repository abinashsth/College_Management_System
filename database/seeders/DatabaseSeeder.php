<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission; // Import the Permission model
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles or retrieve existing ones
        $adminRole = Role::firstOrCreate(['name' => 'Admin'], ['description' => 'Full access to all features']);
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Full access to all features and management']);
        $superAdminUser = User::factory()->create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
        ]);
        $superAdminUser->roles()->attach($superAdminRole);
        
        $accountantRole = Role::firstOrCreate(['name' => 'Accountant'], ['description' => 'Limited access to Account module']);
        $examinerRole = Role::firstOrCreate(['name' => 'Examiner'], ['description' => 'Limited access to Exam module']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher'], ['description' => 'Manage classes and view student profiles']);
        $studentRole = Role::firstOrCreate(['name' => 'Student'], ['description' => 'View own profile and enrolled courses']);

        // Create users and assign roles
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $adminUser->roles()->attach($adminRole);

        $accountantUser = User::factory()->create([
            'name' => 'Accountant User',
            'email' => 'accountant@example.com',
        ]);
        $accountantUser->roles()->attach($accountantRole);

        $examinerUser = User::factory()->create([
            'name' => 'Examiner User',
            'email' => 'examiner@example.com',
        ]);
        $examinerUser->roles()->attach($examinerRole);

        $teacherUser = User::factory()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
        ]);
        $teacherUser->roles()->attach($teacherRole);

        $studentUser = User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
        ]);
        $studentUser->roles()->attach($studentRole);

        // Seed permissions and attach to roles
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $adminRole->permissions()->attach($permission->id);
        }
    }
}

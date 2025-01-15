<?php
namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UpdatedDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        Permission::truncate();
        Role::truncate();
        
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define and create permissions
        $permissionsData = [
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Role Management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            
            // Student Management
            'view_students',
            'create_students',
            'edit_students',
            'delete_students',
            
            // Course Management
            'view_courses',
            'create_courses',
            'edit_courses',
            'delete_courses',
            
            // Exam Management
            'view_exams',
            'create_exams',
            'edit_exams',
            'delete_exams',
            
            // Account Management
            'view_accounts',
            'create_accounts',
            'edit_accounts',
            'delete_accounts',
        ];

        foreach ($permissionsData as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Define roles and their permissions
        $rolesData = [
            'Super Admin' => [
                'description' => 'Full access to all features and management',
                'permissions' => Permission::all()
            ],
            'Admin' => [
                'description' => 'Full access to all features',
                'permissions' => Permission::all()
            ],
            'Accountant' => [
                'description' => 'Limited access to Account module',
                'permissions' => [
                    'view_accounts',
                    'create_accounts',
                    'edit_accounts',
                    'delete_accounts'
                ]
            ],
            'Examiner' => [
                'description' => 'Limited access to Exam module',
                'permissions' => [
                    'view_exams',
                    'create_exams',
                    'edit_exams',
                    'delete_exams'
                ]
            ],
            'Teacher' => [
                'description' => 'Manage classes and view student profiles',
                'permissions' => [
                    'view_students',
                    'view_courses',
                    'edit_courses',
                    'view_exams',
                    'create_exams',
                    'edit_exams'
                ]
            ],
            'Student' => [
                'description' => 'View own profile and enrolled courses',
                'permissions' => [
                    'view_courses',
                    'view_exams'
                ]
            ],
        ];

        // Create roles and assign permissions
        foreach ($rolesData as $roleName => $roleInfo) {
            $role = Role::create([
                'name' => $roleName,
                'description' => $roleInfo['description']
            ]);

            $permissions = is_array($roleInfo['permissions']) 
                ? Permission::whereIn('name', $roleInfo['permissions'])->get() 
                : $roleInfo['permissions'];
                
            $role->syncPermissions($permissions);
        }

        // Define users and their roles
        $usersData = [
            [
                'name' => 'Super Admin User',
                'email' => 'superadmin@example.com',
                'password' => 'password123',
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password123',
                'role' => 'Admin'
            ],
            [
                'name' => 'Accountant User',
                'email' => 'accountant@example.com',
                'password' => 'password123',
                'role' => 'Accountant'
            ],
            [
                'name' => 'Examiner User',
                'email' => 'examiner@example.com',
                'password' => 'password123',
                'role' => 'Examiner'
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@example.com',
                'password' => 'password123',
                'role' => 'Teacher'
            ],
            [
                'name' => 'Student User',
                'email' => 'student@example.com',
                'password' => 'password123',
                'role' => 'Student'
            ],
        ];

        // Create users and assign roles
        foreach ($usersData as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ]
            );
            
            $user->assignRole($userData['role']);
        }

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

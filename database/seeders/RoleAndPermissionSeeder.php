<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Truncate tables to avoid duplicate entries
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();

        // Create permissions
        $permissions = [
            'view dashboard',
            'view students',
            'create students',
            'edit students',
            'delete students',
            'view classes',
            'create classes',
            'edit classes',
            'delete classes',
            'manage exams',
            'manage accounts',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $superAdmin = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'view dashboard',
            'view students',
            'create students',
            'edit students',
            'view classes',
            'create classes',
            'edit classes',
            'manage exams',
            'manage accounts',
            'view users'
        ]);

        $teacher = Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $teacher->givePermissionTo([
            'view dashboard',
            'view students',
            'view classes',
            'manage exams'
        ]);

        // Create a super-admin user
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole($superAdmin);

        // Create an admin user
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole($admin);

        // Create a teacher user
        $user = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole($teacher);
    }
} 
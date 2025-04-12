<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ExistingDataSeeder extends Seeder
{
    public function run()
    {
        // Your existing users
        $users = [
            [
                'name' => 'Existing User',
                'email' => 'existing@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ],
            // Add more users here
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::create($userData);
            $user->assignRole($role);
        }
    }
} 
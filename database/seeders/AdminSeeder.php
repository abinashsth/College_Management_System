<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@admin.com',
            'password' => Hash::make('superadmin123')
        ]);
        $superAdmin->assignRole('super-admin');

        // Create Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123')
        ]);
        $admin->assignRole('admin');

        // Create Teacher
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@admin.com',
            'password' => Hash::make('teacher123')
        ]);
        $teacher->assignRole('teacher');

        // Output confirmation
        \Log::info('Admin users seeded successfully');
    }
} 
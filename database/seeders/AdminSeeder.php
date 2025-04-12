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
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('superadmin123')
            ]
        );
        $superAdmin->assignRole('super-admin');

        // Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123')
            ]
        );
        $admin->assignRole('admin');

        // Create Teacher
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@admin.com'],
            [
                'name' => 'Teacher User',
                'password' => Hash::make('teacher123')
            ]
        );
        $teacher->assignRole('teacher');

        // Create Accountant
        $accountant = User::firstOrCreate(
            ['email' => 'accountant@admin.com'],
            [
                'name' => 'Accountant User',
                'password' => Hash::make('accountant123')
            ]
        );
        $accountant->assignRole('accountant');

        // Create Examiner
        $examiner = User::firstOrCreate(
            ['email' => 'examiner@admin.com'],
            [
                'name' => 'Examiner User',
                'password' => Hash::make('examiner123')
            ]
        );
        $examiner->assignRole('examiner');

        // Create Student
        $student = User::firstOrCreate(
            ['email' => 'student@admin.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('student123')
            ]
        );
        $student->assignRole('student');

        // Output confirmation
        $this->command->info('Admin users seeded successfully');
        $this->command->info('Credentials:');
        $this->command->info('Super Admin - Email: superadmin@admin.com, Password: superadmin123');
        $this->command->info('Admin - Email: admin@admin.com, Password: admin123');
        $this->command->info('Teacher - Email: teacher@admin.com, Password: teacher123');
        $this->command->info('Accountant - Email: accountant@admin.com, Password: accountant123');
        $this->command->info('Examiner - Email: examiner@admin.com, Password: examiner123');
        $this->command->info('Student - Email: student@admin.com, Password: student123');
    }
} 
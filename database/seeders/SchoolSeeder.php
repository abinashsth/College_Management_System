<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        School::create([
            'name' => 'Springfield High School',
            'address' => '123 Education Lane, Springfield, ST 12345',
            'phone' => '(555) 123-4567',
            'email' => 'info@springfieldhigh.edu',
            'is_active' => true,
        ]);
    }
}

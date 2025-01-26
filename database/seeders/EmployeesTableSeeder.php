<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
    DB::table('employees')->insert([
        ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'position' => 'Manager'],
        ['name' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'position' => 'Developer'],
    ]);
    }
}

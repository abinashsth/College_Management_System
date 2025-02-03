<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;    
class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        Employee::create([
        ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'department' => 'Manager', 'designation' => 'Manager', 'contact' => '9814798598', 'status' => 'Active'],
        ['name' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'department' => 'Developer', 'designation' => 'Developer', 'contact' => '9814785698', 'status' => 'Active'],
            ]);
    }
}

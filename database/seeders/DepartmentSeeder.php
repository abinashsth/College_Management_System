<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                    //
            // Define departments
            $departments = [
                ['name' => 'HR'],
                ['name' => 'IT'],
                ['name' => 'Finance'],
                ['name' => 'Marketing'],
                ['name' => 'Operations'],
            ];

            // Insert into database
            Department::insert($departments);

    }
}

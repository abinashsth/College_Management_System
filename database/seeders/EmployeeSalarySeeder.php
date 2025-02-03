<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmployeeSalary::create([
            'employee_name' => 'John Doe',
            'salary' => 5000.00,
            'salary_date' => '2023-10-01',
        ]);

        EmployeeSalary::create([
            'employee_name' => 'Jane Smith',
            'salary' => 6000.00, 
            'salary_date' => '2023-10-01',
        ]);

        EmployeeSalary::create([
            'employee_name' => 'Alice Johnson',
            'salary' => 5500.00,
            'salary_date' => '2023-10-01',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeSalary;

class EmployeeSalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = Employee::first(); // Assuming you have employees


        if ($employee) {
            
            EmployeeSalary::create([
                'employee_id' => $employee->id,
                'basic_salary' => 50000,
                'allowances' => 5000,
                'deductions' => 2000,
                'status' => 'Paid',
                'payment_date' => now(), // Set default date
            ]);
            
        }
    }
}

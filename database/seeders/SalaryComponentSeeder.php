<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalaryComponent;

class SalaryComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salaryComponents = [
            [
                'name' => 'Basic Salary',
                'type' => 'Allowance',
                'status' => true,
                'description' => 'Basic salary of the employee',
                'amount' => 10000
            ],
            [
                'name' => 'House Rent Allowance',
                'type' => 'Allowance',
                'status' => true,
                'description' => 'House rent allowance of the employee',
                'amount' => 1000
            ],
            [
                'name' => 'Transport Allowance',
                'type' => 'Allowance',
                'status' => true,
                'description' => 'Transport allowance of the employee',
                'amount' => 1000
            ],
            [
                'name' => 'Medical Allowance',
                'type' => 'Allowance',
                'status' => true,
                'description' => 'Medical allowance of the employee',
                'amount' => 1000
            ],
            [
                'name' => 'Other Allowance',
                'type' => 'Allowance',
                'status' => true,
                'description' => 'Other allowance of the employee',
                'amount' => 1000
            ],
            [
                'name' => 'Provident Fund',
                'type' => 'Deduction',
                'status' => true,
                'description' => 'Provident fund of the employee',
                'amount' => 1000
            ],
            [
                'name' => 'Professional Tax',
                'type' => 'Deduction',
                'status' => true,
                'description' => 'Professional tax of the employee',
                'amount' => 1000
            ],
            [
                'name' => 'Other Deduction',
                'type' => 'Deduction',
                'status' => true,
                'description' => 'Other deduction of the employee',
                'amount' => 1000
            ],
        ];

        foreach ($salaryComponents as $component) {
            SalaryComponent::create($component);
        }       
        
    }
}

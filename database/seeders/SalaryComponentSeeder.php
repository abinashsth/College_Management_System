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
                'description' => 'Basic salary of the employee' 
            ],
            [
                'name' => 'House Rent Allowance',
                'type' => 'Allowance',
                'status' => true,
                'description' => 'House rent allowance of the employee' 
            ],
            [
                'name' => 'Transport Allowance',
                'type' => 'Allowance',
                'status' => true,
                'description' => 'Transport allowance of the employee'   
            ],
            [
                'name' => 'Medical Allowance',
                'type' => 'Allowance',
                'status' => true,
                'description' => 'Medical allowance of the employee'        
            ],
            [
                'name' => 'Other Allowance',
                'type' => 'Allowance',
                'status' => true,
                'description' => 'Other allowance of the employee'  
            ],
            [
                'name' => 'Provident Fund',
                'type' => 'Deduction',
                'status' => true,
                'description' => 'Provident fund of the employee'   
            ],
            [
                'name' => 'Professional Tax',
                'type' => 'Deduction',
                'status' => true,
                'description' => 'Professional tax of the employee' 
            ],
            [
                'name' => 'Other Deduction',
                'type' => 'Deduction',
                'status' => true,
                'description' => 'Other deduction of the employee'      
            ],
        ];

        foreach ($salaryComponents as $component) {
            SalaryComponent::create($component);
        }       
        
    }
}

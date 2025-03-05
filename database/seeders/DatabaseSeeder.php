<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache before seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->call([
            PermissionSeeder::class,    // First create permissions
            RoleSeeder::class,          // Then create roles
            AdminSeeder::class          // Finally create admin users
        ]);



         // Create employees
         $employees = [
            [
                'employee_id' => 'EMP-001',
                'name' => 'Dr. Rajesh Kumar',
                'department_id' => 1,
                'designation' => 'Professor',
                'basic_salary' => 85000,
                'allowances' => 25000,
                'deductions' => 12000,
                'email' => 'rajesh@example.com',
                'join_date' => '2020-01-15',
            ],
            [
                'employee_id' => 'EMP-002',
                'name' => 'Dr. Priya Singh',
                'department_id' => 2,
                'designation' => 'Associate Professor',
                'basic_salary' => 72000,
                'allowances' => 18000,
                'deductions' => 10000,
                'email' => 'priya.singh@example.com',
                'join_date' => '2020-03-10',
            ],
            [
                'employee_id' => 'EMP-003',
                'name' => 'Dr. Amit Sharma',
                'department_id' => 3,
                'designation' => 'Professor',
                'basic_salary' => 90000,
                'allowances' => 30000,
                'deductions' => 15000,
                'email' => 'amit.sharma@example.com',
                'join_date' => '2019-07-22',
            ],
            [
                'employee_id' => 'EMP-004',
                'name' => 'Ms. Sneha Gupta',
                'department_id' => 4,
                'designation' => 'Administrative Officer',
                'basic_salary' => 45000,
                'allowances' => 10000,
                'deductions' => 5000,
                'email' => 'sneha.gupta@example.com',
                'join_date' => '2021-02-15',
            ],
            [
                'employee_id' => 'EMP-005',
                'name' => 'Mr. Rahul Verma',
                'department_id' => 5,
                'designation' => 'Assistant Professor',
                'basic_salary' => 60000,
                'allowances' => 15000,
                'deductions' => 8000,
                'email' => 'rahul.verma@example.com',
                'join_date' => '2021-06-10',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }





    }
}
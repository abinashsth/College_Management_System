<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Department;

class UpdateStudentRegistrationNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:update-registration-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update registration numbers for students that do not have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $students = Student::all();
        
        $this->info("Found {$students->count()} students total.");
        
        $departmentStudentCounts = [];
        
        foreach ($students as $student) {
            // Skip if no department
            if (!$student->department_id) {
                $this->warn("Student ID {$student->id} has no department ID. Skipping.");
                continue;
            }
            
            // Get the department
            $department = Department::find($student->department_id);
            if (!$department) {
                $this->warn("Department ID {$student->department_id} not found for student ID {$student->id}. Skipping.");
                continue;
            }
            
            // Get department code
            $departmentCode = strtoupper(substr($department->name, 0, 3));
            
            // Get batch year (use current year if not set)
            $batchYear = $student->batch_year ?? date('Y');
            
            // Get student count for this department and batch
            if (!isset($departmentStudentCounts["{$department->id}-{$batchYear}"])) {
                $departmentStudentCounts["{$department->id}-{$batchYear}"] = 0;
            }
            
            // Generate registration number
            $registrationNumber = Student::generateRegistrationNumber(
                $departmentCode,
                $batchYear,
                $departmentStudentCounts["{$department->id}-{$batchYear}"]
            );
            
            // Update student
            $student->registration_number = $registrationNumber;
            $student->save();
            
            // Increment count
            $departmentStudentCounts["{$department->id}-{$batchYear}"]++;
            
            $this->info("Updated student ID {$student->id} with registration number {$registrationNumber}");
        }
        
        $this->info("Registration numbers updated successfully!");
        
        return Command::SUCCESS;
    }
}

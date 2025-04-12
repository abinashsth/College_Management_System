<?php

namespace App\Console\Commands;

use App\Models\AcademicStructure;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncAcademicStructuresCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academic:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize academic structures with traditional tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting synchronization of academic structures...');
        
        // Start with faculties (top level)
        $faculties = AcademicStructure::where('type', 'faculty')->get();
        $facultyCount = 0;
        
        foreach ($faculties as $faculty) {
            if ($this->syncFaculty($faculty)) {
                $facultyCount++;
            }
        }
        
        $this->info("Synchronized $facultyCount faculties.");
        
        // Then departments (second level)
        $departments = AcademicStructure::where('type', 'department')->get();
        $departmentCount = 0;
        
        foreach ($departments as $department) {
            if ($this->syncDepartment($department)) {
                $departmentCount++;
            }
        }
        
        $this->info("Synchronized $departmentCount departments.");
        
        // Finally programs (third level)
        $programs = AcademicStructure::where('type', 'program')->get();
        $programCount = 0;
        
        foreach ($programs as $program) {
            if ($this->syncProgram($program)) {
                $programCount++;
            }
        }
        
        $this->info("Synchronized $programCount programs.");
        $this->info('Synchronization completed successfully.');
        
        return Command::SUCCESS;
    }
    
    /**
     * Sync a faculty structure to the traditional system
     */
    private function syncFaculty(AcademicStructure $faculty)
    {
        $this->line("Processing faculty: {$faculty->name}");
        
        try {
            // Check if a faculty with this code already exists
            $existingFaculty = Faculty::where('code', $faculty->code)->first();
            
            if ($existingFaculty) {
                $this->line("- Faculty already exists with code {$faculty->code}, updating...");
                $existingFaculty->update([
                    'name' => $faculty->name,
                    'slug' => Str::slug($faculty->name),
                    'description' => $faculty->description,
                    'status' => $faculty->is_active,
                ]);
            } else {
                $this->line("- Creating new faculty with code {$faculty->code}...");
                Faculty::create([
                    'name' => $faculty->name,
                    'slug' => Str::slug($faculty->name),
                    'code' => $faculty->code,
                    'description' => $faculty->description,
                    'status' => $faculty->is_active,
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            $this->error("Failed to sync faculty {$faculty->name}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sync a department structure to the traditional system
     */
    private function syncDepartment(AcademicStructure $department)
    {
        $this->line("Processing department: {$department->name}");
        
        try {
            // A department must have a parent faculty
            if (!$department->parent_id) {
                $this->warn("- Department {$department->name} has no parent, skipping...");
                return false;
            }
            
            $parentStructure = AcademicStructure::find($department->parent_id);
            
            if (!$parentStructure || $parentStructure->type !== 'faculty') {
                $this->warn("- Department {$department->name} doesn't have a faculty parent, skipping...");
                return false;
            }
            
            // Find the corresponding faculty in the traditional system
            $faculty = Faculty::where('code', $parentStructure->code)->first();
            
            if (!$faculty) {
                $this->warn("- Parent faculty not found for department {$department->name}, try syncing faculties first.");
                return false;
            }
            
            // Check if a department with this code already exists
            $existingDepartment = Department::where('code', $department->code)->first();
            
            if ($existingDepartment) {
                $this->line("- Department already exists with code {$department->code}, updating...");
                $existingDepartment->update([
                    'name' => $department->name,
                    'slug' => Str::slug($department->name),
                    'description' => $department->description,
                    'faculty_id' => $faculty->id,
                    'status' => $department->is_active,
                ]);
            } else {
                $this->line("- Creating new department with code {$department->code}...");
                Department::create([
                    'name' => $department->name,
                    'slug' => Str::slug($department->name),
                    'code' => $department->code,
                    'description' => $department->description,
                    'faculty_id' => $faculty->id,
                    'status' => $department->is_active,
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            $this->error("Failed to sync department {$department->name}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sync a program structure to the traditional system
     */
    private function syncProgram(AcademicStructure $program)
    {
        $this->line("Processing program: {$program->name}");
        
        try {
            // A program must have a parent department
            if (!$program->parent_id) {
                $this->warn("- Program {$program->name} has no parent, skipping...");
                return false;
            }
            
            $parentStructure = AcademicStructure::find($program->parent_id);
            
            if (!$parentStructure || $parentStructure->type !== 'department') {
                $this->warn("- Program {$program->name} doesn't have a department parent, skipping...");
                return false;
            }
            
            // Find the corresponding department in the traditional system
            $department = Department::where('code', $parentStructure->code)->first();
            
            if (!$department) {
                $this->warn("- Parent department not found for program {$program->name}, try syncing departments first.");
                return false;
            }
            
            // Check if a program with this code already exists
            $existingProgram = Program::where('code', $program->code)->first();
            
            if ($existingProgram) {
                $this->line("- Program already exists with code {$program->code}, updating...");
                $existingProgram->update([
                    'name' => $program->name,
                    'slug' => Str::slug($program->name),
                    'description' => $program->description,
                    'department_id' => $department->id,
                    'status' => $program->is_active,
                ]);
            } else {
                $this->line("- Creating new program with code {$program->code}...");
                Program::create([
                    'name' => $program->name,
                    'slug' => Str::slug($program->name),
                    'code' => $program->code,
                    'description' => $program->description,
                    'department_id' => $department->id,
                    'status' => $program->is_active,
                    'duration' => 4, // Default values
                    'duration_unit' => 'years',
                    'credit_hours' => 120,
                    'degree_level' => 'Bachelor',
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            $this->error("Failed to sync program {$program->name}: " . $e->getMessage());
            return false;
        }
    }
} 
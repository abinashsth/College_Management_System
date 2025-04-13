<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\AcademicStructure;
use App\Models\Faculty;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateDepartmentsToAcademicStructures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:departments-to-academic-structures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing departments to the academic_structures table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting department migration to academic_structures...');
        
        $departments = Department::all();
        $this->info("Found {$departments->count()} departments to migrate");
        
        $count = 0;
        
        foreach ($departments as $department) {
            $this->info("Processing department: {$department->name} (Code: {$department->code})");
            
            // Check if it already exists
            $exists = AcademicStructure::where('code', $department->code)
                ->orWhere(function($query) use ($department) {
                    $query->where('type', 'department')
                          ->whereJsonContains('metadata->department_id', $department->id);
                })
                ->exists();
                
            if ($exists) {
                $this->warn("Department already exists in academic_structures. Skipping.");
                continue;
            }
            
            DB::beginTransaction();
            
            try {
                $academicStructure = new AcademicStructure();
                $academicStructure->name = $department->name;
                $academicStructure->type = 'department';
                $academicStructure->code = $department->code;
                $academicStructure->description = $department->description;
                
                // Set faculty as parent if exists
                if ($department->faculty_id) {
                    $faculty = Faculty::find($department->faculty_id);
                    if ($faculty) {
                        $parentStructure = AcademicStructure::where('code', $faculty->code)->first();
                        if ($parentStructure) {
                            $academicStructure->parent_id = $parentStructure->id;
                        }
                    }
                }
                
                $academicStructure->is_active = $department->status ?? true;
                $academicStructure->metadata = [
                    'department_id' => $department->id,
                ];
                $academicStructure->save();
                
                DB::commit();
                $count++;
                $this->info("Migrated department successfully.");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error migrating department: " . $e->getMessage());
                Log::error("Error migrating department {$department->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Migration completed. Migrated {$count} departments.");
    }
} 
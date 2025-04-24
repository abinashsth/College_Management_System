<?php

namespace App\Http\Controllers;

use App\Models\AcademicStructure;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class AcademicStructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load parent relationship to avoid N+1 query problem
        $academicStructures = AcademicStructure::with('parent')->get();
        return view('settings.academic-structure.index', compact('academicStructures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentStructures = AcademicStructure::where('type', '!=', 'program')->get();
        return view('settings.academic-structure.create', compact('parentStructures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:academic_structures',
            'type' => 'required|string|in:faculty,department,program',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:academic_structures,id',
            'is_active' => 'boolean',
        ]);

        // Validate parent-child type relationship
        if ($request->parent_id) {
            $parent = AcademicStructure::findOrFail($request->parent_id);
            
            if ($parent->type === 'faculty' && $request->type === 'faculty') {
                return back()->with('error', 'A faculty cannot be a child of another faculty')->withInput();
            }
            
            if ($parent->type === 'department' && !in_array($request->type, ['program'])) {
                return back()->with('error', 'A department can only have programs as children')->withInput();
            }
            
            if ($parent->type === 'program') {
                return back()->with('error', 'A program cannot have children')->withInput();
            }
        } else {
            // Top-level items can only be faculties
            if ($request->type !== 'faculty') {
                return back()->with('error', 'Only faculties can be top-level items')->withInput();
            }
        }

        // Use database transaction to ensure data integrity across both systems
        DB::beginTransaction();
        try {
            // Create the academic structure
            $academicStructure = AcademicStructure::create($validated);
            
            // Sync with traditional tables based on type
            switch ($academicStructure->type) {
                case 'faculty':
                    $this->createFaculty($academicStructure);
                    break;
                    
                case 'department':
                    $this->createDepartment($academicStructure);
                    break;
                    
                case 'program':
                    $this->createProgram($academicStructure);
                    break;
            }
            
            DB::commit();
            return redirect()->route('settings.academic-structure.index')
                ->with('success', 'Academic structure created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create academic structure: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicStructure $academicStructure)
    {
        // Eager load parent and children relationships to avoid N+1 query problem
        $academicStructure->load('parent', 'children');
        return view('settings.academic-structure.show', compact('academicStructure'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicStructure $academicStructure)
    {
        $parents = AcademicStructure::where('id', '!=', $academicStructure->id)
            ->where('type', '!=', 'program')
            ->get();
            
        return view('settings.academic-structure.edit', compact('academicStructure', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicStructure $academicStructure)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:academic_structures,code,' . $academicStructure->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:academic_structures,id',
            'is_active' => 'boolean',
        ]);

        // Validate parent-child relationship
        if ($request->parent_id) {
            // Cannot set parent to itself or its descendants
            if ($academicStructure->id == $request->parent_id) {
                return back()->with('error', 'Cannot set parent to itself')->withInput();
            }

            // Check if the proposed parent is not a descendant of the current item
            $parent = AcademicStructure::findOrFail($request->parent_id);
            $currentParentId = $parent->parent_id;
            
            while ($currentParentId) {
                if ($currentParentId == $academicStructure->id) {
                    return back()->with('error', 'Cannot set a descendant as parent')->withInput();
                }
                $currentParent = AcademicStructure::find($currentParentId);
                $currentParentId = $currentParent ? $currentParent->parent_id : null;
            }
            
            // Validate parent-child type relationship
            if ($parent->type === 'faculty' && $academicStructure->type === 'faculty') {
                return back()->with('error', 'A faculty cannot be a child of another faculty')->withInput();
            }
            
            if ($parent->type === 'department' && !in_array($academicStructure->type, ['program'])) {
                return back()->with('error', 'A department can only have programs as children')->withInput();
            }
            
            if ($parent->type === 'program') {
                return back()->with('error', 'A program cannot have children')->withInput();
            }
        } else {
            // Top-level items can only be faculties
            if ($academicStructure->type !== 'faculty') {
                return back()->with('error', 'Only faculties can be top-level items')->withInput();
            }
        }

        // Use database transaction to ensure data integrity
        DB::beginTransaction();
        try {
            $academicStructure->update($validated);
            
            // Sync with traditional tables based on type
            switch ($academicStructure->type) {
                case 'faculty':
                    $this->updateFaculty($academicStructure);
                    break;
                    
                case 'department':
                    $this->updateDepartment($academicStructure);
                    break;
                    
                case 'program':
                    $this->updateProgram($academicStructure);
                    break;
            }
            
            DB::commit();
            return redirect()->route('settings.academic-structure.index')
                ->with('success', 'Academic structure updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update academic structure: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicStructure $academicStructure)
    {
        // Check if the structure has children
        if ($academicStructure->children()->count() > 0) {
            return back()->with('error', 'Cannot delete an academic structure with children');
        }

        DB::beginTransaction();
        try {
            // Delete from traditional tables based on type
            switch ($academicStructure->type) {
                case 'faculty':
                    $this->deleteFaculty($academicStructure);
                    break;
                    
                case 'department':
                    $this->deleteDepartment($academicStructure);
                    break;
                    
                case 'program':
                    $this->deleteProgram($academicStructure);
                    break;
            }
            
            $academicStructure->delete();
            DB::commit();
            return redirect()->route('settings.academic-structure.index')
                ->with('success', 'Academic structure deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete academic structure: ' . $e->getMessage());
        }
    }
    
    /**
     * Create a faculty record in the traditional system
     */
    private function createFaculty(AcademicStructure $academicStructure)
    {
        // Check if a faculty with this code already exists
        $existingFaculty = Faculty::where('code', $academicStructure->code)->first();
        if (!$existingFaculty) {
            Faculty::create([
                'name' => $academicStructure->name,
                'slug' => Str::slug($academicStructure->name),
                'code' => $academicStructure->code,
                'description' => $academicStructure->description,
                'status' => $academicStructure->is_active,
            ]);
        }
    }
    
    /**
     * Create a department record in the traditional system
     */
    private function createDepartment(AcademicStructure $academicStructure)
    {
        // A department must have a parent faculty
        if ($academicStructure->parent_id) {
            $parentStructure = AcademicStructure::find($academicStructure->parent_id);
            
            if ($parentStructure && $parentStructure->type === 'faculty') {
                // Find the corresponding faculty in the traditional system
                $faculty = Faculty::where('code', $parentStructure->code)->first();
                
                if ($faculty) {
                    // Check if a department with this code already exists
                    $existingDepartment = Department::where('code', $academicStructure->code)->first();
                    if (!$existingDepartment) {
                        Department::create([
                            'name' => $academicStructure->name,
                            'slug' => Str::slug($academicStructure->name),
                            'code' => $academicStructure->code,
                            'description' => $academicStructure->description,
                            'faculty_id' => $faculty->id,
                            'status' => $academicStructure->is_active,
                        ]);
                    }
                }
            }
        }
    }
    
    /**
     * Create a program record in the traditional system
     */
    private function createProgram(AcademicStructure $academicStructure)
    {
        // A program must have a parent department
        if ($academicStructure->parent_id) {
            $parentStructure = AcademicStructure::find($academicStructure->parent_id);
            
            if ($parentStructure && $parentStructure->type === 'department') {
                // Find the corresponding department in the traditional system
                $department = Department::where('code', $parentStructure->code)->first();
                
                if ($department) {
                    // Check if a program with this code already exists
                    $existingProgram = Program::where('code', $academicStructure->code)->first();
                    if (!$existingProgram) {
                        Program::create([
                            'name' => $academicStructure->name,
                            'slug' => Str::slug($academicStructure->name),
                            'code' => $academicStructure->code,
                            'description' => $academicStructure->description,
                            'department_id' => $department->id,
                            'status' => $academicStructure->is_active,
                            'duration' => 4, // Default values
                            'duration_unit' => 'years',
                            'credit_hours' => 120,
                            'degree_level' => 'Bachelor',
                        ]);
                    }
                }
            }
        }
    }
    
    /**
     * Update a faculty record in the traditional system
     */
    private function updateFaculty(AcademicStructure $academicStructure)
    {
        $faculty = Faculty::where('code', $academicStructure->code)->first();
        
        if ($faculty) {
            $faculty->update([
                'name' => $academicStructure->name,
                'slug' => Str::slug($academicStructure->name),
                'description' => $academicStructure->description,
                'status' => $academicStructure->is_active,
            ]);
        } else {
            // If faculty doesn't exist yet, create it
            $this->createFaculty($academicStructure);
        }
    }
    
    /**
     * Update a department record in the traditional system
     */
    private function updateDepartment(AcademicStructure $academicStructure)
    {
        $department = Department::where('code', $academicStructure->code)->first();
        
        if ($department) {
            // Update basic department info
            $department->name = $academicStructure->name;
            $department->slug = Str::slug($academicStructure->name);
            $department->description = $academicStructure->description;
            $department->status = $academicStructure->is_active;
            
            // If parent changed, update faculty_id
            if ($academicStructure->parent_id) {
                $parentStructure = AcademicStructure::find($academicStructure->parent_id);
                
                if ($parentStructure && $parentStructure->type === 'faculty') {
                    $faculty = Faculty::where('code', $parentStructure->code)->first();
                    
                    if ($faculty) {
                        $department->faculty_id = $faculty->id;
                    }
                }
            }
            
            $department->save();
        } else {
            // If department doesn't exist yet, create it
            $this->createDepartment($academicStructure);
        }
    }
    
    /**
     * Update a program record in the traditional system
     */
    private function updateProgram(AcademicStructure $academicStructure)
    {
        $program = Program::where('code', $academicStructure->code)->first();
        
        if ($program) {
            // Update basic program info
            $program->name = $academicStructure->name;
            $program->slug = Str::slug($academicStructure->name);
            $program->description = $academicStructure->description;
            $program->status = $academicStructure->is_active;
            
            // If parent changed, update department_id
            if ($academicStructure->parent_id) {
                $parentStructure = AcademicStructure::find($academicStructure->parent_id);
                
                if ($parentStructure && $parentStructure->type === 'department') {
                    $department = Department::where('code', $parentStructure->code)->first();
                    
                    if ($department) {
                        $program->department_id = $department->id;
                    }
                }
            }
            
            $program->save();
        } else {
            // If program doesn't exist yet, create it
            $this->createProgram($academicStructure);
        }
    }
    
    /**
     * Delete a faculty record from the traditional system
     */
    private function deleteFaculty(AcademicStructure $academicStructure)
    {
        $faculty = Faculty::where('code', $academicStructure->code)->first();
        
        if ($faculty) {
            // Check if the faculty has departments
            if ($faculty->departments()->count() > 0) {
                throw new \Exception('Cannot delete faculty with associated departments.');
            }
            
            $faculty->delete();
        }
    }
    
    /**
     * Delete a department record from the traditional system
     */
    private function deleteDepartment(AcademicStructure $academicStructure)
    {
        $department = Department::where('code', $academicStructure->code)->first();
        
        if ($department) {
            // Check if the department has programs, teachers, or students
            if ($department->programs()->count() > 0) {
                throw new \Exception('Cannot delete department with associated programs.');
            }
            
            if ($department->teachers()->count() > 0) {
                throw new \Exception('Cannot delete department with associated teachers.');
            }
            
            if ($department->students()->count() > 0) {
                throw new \Exception('Cannot delete department with associated students.');
            }
            
            $department->delete();
        }
    }
    
    /**
     * Delete a program record from the traditional system
     */
    private function deleteProgram(AcademicStructure $academicStructure)
    {
        $program = Program::where('code', $academicStructure->code)->first();
        
        if ($program) {
            // Check if the program has students
            if ($program->students()->count() > 0) {
                throw new \Exception('Cannot delete program with enrolled students.');
            }
            
            // Detach any related courses
            $program->courses()->detach();
            
            $program->delete();
        }
    }

    /**
     * Synchronize all academic structures with traditional tables
     */
    public function synchronizeAll()
    {
        // Start a database transaction explicitly
        DB::beginTransaction();
        
        try {
            // Check if database tables exist before proceeding
            $requiredTables = ['faculties', 'departments', 'programs'];
            foreach ($requiredTables as $table) {
                if (!Schema::hasTable($table)) {
                    return back()->with('error', "Required table '{$table}' does not exist. Please run migrations first.");
                }
            }
            
            // First, check if we need to fix parent-child relationships
            $orphanDepartments = AcademicStructure::where('type', 'department')
                ->whereNull('parent_id')
                ->get();
                
            if ($orphanDepartments->count() > 0) {
                // Look for a default faculty to attach orphaned departments to
                $defaultFaculty = AcademicStructure::where('type', 'faculty')->first();
                
                if (!$defaultFaculty) {
                    // Create a default faculty if none exists
                    $defaultFaculty = AcademicStructure::create([
                        'name' => 'Default Faculty',
                        'code' => 'DEFAULT-FAC',
                        'type' => 'faculty',
                        'description' => 'Default faculty for orphaned departments',
                        'is_active' => true,
                    ]);
                    
                    // We need to create it in the traditional system too
                    Faculty::create([
                        'name' => $defaultFaculty->name,
                        'slug' => Str::slug($defaultFaculty->name),
                        'code' => $defaultFaculty->code,
                        'description' => $defaultFaculty->description,
                        'status' => true,
                        'academic_structure_id' => $defaultFaculty->id,
                    ]);
                }
                
                // Attach orphaned departments to the default faculty
                foreach ($orphanDepartments as $orphan) {
                    $orphan->parent_id = $defaultFaculty->id;
                    $orphan->save();
                    
                    \Log::info("Fixed orphaned department: {$orphan->name} by attaching to {$defaultFaculty->name}");
                }
            }
            
            // Check for orphaned programs
            $orphanPrograms = AcademicStructure::where('type', 'program')
                ->whereNull('parent_id')
                ->get();
                
            if ($orphanPrograms->count() > 0) {
                // Look for a default department to attach orphaned programs to
                $defaultDepartment = AcademicStructure::where('type', 'department')->first();
                
                if (!$defaultDepartment) {
                    // If no department exists at all, we need to first ensure we have a faculty
                    $defaultFaculty = AcademicStructure::where('type', 'faculty')->first();
                    
                    if (!$defaultFaculty) {
                        // Create a default faculty if none exists
                        $defaultFaculty = AcademicStructure::create([
                            'name' => 'Default Faculty',
                            'code' => 'DEFAULT-FAC',
                            'type' => 'faculty',
                            'description' => 'Default faculty for orphaned departments',
                            'is_active' => true,
                        ]);
                        
                        // We need to create it in the traditional system too
                        Faculty::create([
                            'name' => $defaultFaculty->name,
                            'slug' => Str::slug($defaultFaculty->name),
                            'code' => $defaultFaculty->code,
                            'description' => $defaultFaculty->description,
                            'status' => true,
                            'academic_structure_id' => $defaultFaculty->id,
                        ]);
                    }
                    
                    // Create a default department
                    $defaultDepartment = AcademicStructure::create([
                        'name' => 'Default Department',
                        'code' => 'DEFAULT-DEP',
                        'type' => 'department',
                        'description' => 'Default department for orphaned programs',
                        'parent_id' => $defaultFaculty->id,
                        'is_active' => true,
                    ]);
                    
                    // Create in traditional system too
                    $faculty = Faculty::where('code', $defaultFaculty->code)->first();
                    
                    Department::create([
                        'name' => $defaultDepartment->name,
                        'slug' => Str::slug($defaultDepartment->name),
                        'code' => $defaultDepartment->code,
                        'description' => $defaultDepartment->description,
                        'faculty_id' => $faculty->id,
                        'status' => true,
                        'academic_structure_id' => $defaultDepartment->id,
                    ]);
                }
                
                // Attach orphaned programs to the default department
                foreach ($orphanPrograms as $orphan) {
                    $orphan->parent_id = $defaultDepartment->id;
                    $orphan->save();
                    
                    \Log::info("Fixed orphaned program: {$orphan->name} by attaching to {$defaultDepartment->name}");
                }
            }
            
            // Instead of including the sync script directly, we'll run our own sync code here
            // This is to avoid transaction conflicts between the controller and included script
            
            // Sync faculties
            $faculties = AcademicStructure::where('type', 'faculty')->get();
            foreach ($faculties as $faculty) {
                $this->syncFaculty($faculty);
            }
            
            // Sync departments
            $departments = AcademicStructure::where('type', 'department')->get();
            foreach ($departments as $department) {
                $this->syncDepartment($department);
            }
            
            // Sync programs
            $programs = AcademicStructure::where('type', 'program')->get();
            foreach ($programs as $program) {
                $this->syncProgram($program);
            }
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('settings.academic-structure.index')
                ->with('success', 'All academic structures synchronized successfully with traditional tables');
        } catch (\Exception $e) {
            // Log the detailed error for debugging
            \Log::error('Academic structure sync error: ' . $e->getMessage());
            
            // Roll back transaction
            DB::rollBack();
            
            return back()->with('error', 'Failed to synchronize academic structures: ' . $e->getMessage());
        }
    }
    
    /**
     * Sync a faculty to the traditional table
     */
    private function syncFaculty(AcademicStructure $faculty)
    {
        // Check if a faculty with this code already exists
        $existingFaculty = Faculty::where('code', $faculty->code)->first();
        
        if ($existingFaculty) {
            // Update existing faculty
            $updateData = [
                'name' => $faculty->name,
                'description' => $faculty->description,
                'slug' => Str::slug($faculty->name),
                'status' => $faculty->is_active,
                'academic_structure_id' => $faculty->id,
            ];
            
            $existingFaculty->update($updateData);
        } else {
            // Create new faculty
            Faculty::create([
                'name' => $faculty->name,
                'slug' => Str::slug($faculty->name),
                'code' => $faculty->code,
                'description' => $faculty->description,
                'status' => $faculty->is_active,
                'academic_structure_id' => $faculty->id,
            ]);
        }
    }
    
    /**
     * Sync a department to the traditional table
     */
    private function syncDepartment(AcademicStructure $department)
    {
        // A department must have a parent faculty
        if (!$department->parent_id) {
            return;
        }
        
        $parentStructure = AcademicStructure::find($department->parent_id);
        
        if (!$parentStructure || $parentStructure->type !== 'faculty') {
            return;
        }
        
        // Find the corresponding faculty in the traditional system
        $faculty = Faculty::where('code', $parentStructure->code)->first();
        
        if (!$faculty) {
            return;
        }
        
        // Check if a department with this code already exists
        $existingDepartment = Department::where('code', $department->code)->first();
        
        if ($existingDepartment) {
            // Update existing department
            $updateData = [
                'name' => $department->name,
                'slug' => Str::slug($department->name),
                'description' => $department->description,
                'faculty_id' => $faculty->id,
                'status' => $department->is_active,
                'academic_structure_id' => $department->id,
            ];
            
            $existingDepartment->update($updateData);
        } else {
            // Create new department
            Department::create([
                'name' => $department->name,
                'slug' => Str::slug($department->name),
                'code' => $department->code,
                'description' => $department->description,
                'faculty_id' => $faculty->id,
                'status' => $department->is_active,
                'academic_structure_id' => $department->id,
            ]);
        }
    }
    
    /**
     * Sync a program to the traditional table
     */
    private function syncProgram(AcademicStructure $program)
    {
        // A program must have a parent department
        if (!$program->parent_id) {
            return;
        }
        
        $parentStructure = AcademicStructure::find($program->parent_id);
        
        if (!$parentStructure || $parentStructure->type !== 'department') {
            return;
        }
        
        // Find the corresponding department in the traditional system
        $department = Department::where('code', $parentStructure->code)->first();
        
        if (!$department) {
            return;
        }
        
        // Check if a program with this code already exists
        $existingProgram = Program::where('code', $program->code)->first();
        
        if ($existingProgram) {
            // Update existing program
            $updateData = [
                'name' => $program->name,
                'slug' => Str::slug($program->name),
                'description' => $program->description,
                'department_id' => $department->id,
                'status' => $program->is_active,
                'academic_structure_id' => $program->id,
                // Keep existing duration, credit_hours, etc. if not in AcademicStructure
                'duration' => $existingProgram->duration ?? 4,
                'duration_unit' => $existingProgram->duration_unit ?? 'years',
                'credit_hours' => $existingProgram->credit_hours ?? 120,
                'degree_level' => $existingProgram->degree_level ?? 'Bachelor',
            ];
            
            $existingProgram->update($updateData);
        } else {
            // Create new program
            Program::create([
                'name' => $program->name,
                'slug' => Str::slug($program->name),
                'code' => $program->code,
                'description' => $program->description,
                'department_id' => $department->id,
                'status' => $program->is_active,
                'academic_structure_id' => $program->id,
                'duration' => 4, // Default value
                'duration_unit' => 'years', // Default value
                'credit_hours' => 120, // Default value
                'degree_level' => 'Bachelor', // Default value
            ]);
        }
    }
}

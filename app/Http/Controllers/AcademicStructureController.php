<?php

namespace App\Http\Controllers;

use App\Models\AcademicStructure;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        try {
            // Load our standalone script
            require_once base_path('sync-academic-structures.php');
            return redirect()->route('settings.academic-structure.index')
                ->with('success', 'All academic structures synchronized successfully with traditional tables');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to synchronize academic structures: ' . $e->getMessage());
        }
    }
}

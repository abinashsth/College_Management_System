<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::with(['faculty', 'head'])->paginate(10);
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $faculties = Faculty::where('status', true)->get();
        $teachers = User::whereHas('roles', function($query) {
            $query->where('name', 'teacher');
        })->get();
        
        return view('departments.create', compact('faculties', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:departments',
                'slug' => 'nullable|string|max:255|unique:departments',
                'code' => 'required|string|max:20|unique:departments',
                'faculty_id' => 'required|exists:faculties,id',
                'description' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'website' => 'nullable|url|max:255',
                'address' => 'nullable|string|max:255',
                'established_date' => 'nullable|date',
                'status' => 'nullable|boolean',
            ]);

            // Log validation result
            \Log::info('Department validation passed', ['data' => $validated]);

            // Generate slug if not provided
            if (!isset($validated['slug']) || empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Handle logo upload if provided
            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                $filename = time() . '_' . $logoFile->getClientOriginalName();
                $logoFile->storeAs('public/department_logos', $filename);
                $validated['logo'] = $filename;
            }

            // Ensure the type is set to 'department'
            $validated['type'] = 'department';

            // Log the final data before database operation
            \Log::info('Department data before creation', ['data' => $validated]);

            DB::beginTransaction();
            
            try {
                $department = Department::create($validated);
                
                // Create corresponding entry in academic_structures table
                $academicStructure = new \App\Models\AcademicStructure();
                $academicStructure->name = $department->name;
                $academicStructure->type = 'department';
                $academicStructure->code = $department->code;
                $academicStructure->description = $department->description;
                
                // Set faculty as parent if exists
                if ($department->faculty_id) {
                    $faculty = \App\Models\Faculty::find($department->faculty_id);
                    $parentStructure = \App\Models\AcademicStructure::where('code', $faculty->code)->first();
                    if ($parentStructure) {
                        $academicStructure->parent_id = $parentStructure->id;
                    }
                }
                
                $academicStructure->is_active = $department->status ?? true;
                $academicStructure->metadata = [
                    'department_id' => $department->id,
                ];
                $academicStructure->save();
                
                // Log the created department
                \Log::info('Department created', ['department' => $department->toArray()]);
                
                DB::commit();
                return redirect()->route('departments.index')
                    ->with('success', 'Department created successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                // Log the exception for debugging
                \Log::error('Department creation error: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                
                return back()->withErrors(['message' => 'An error occurred while creating the department: ' . $e->getMessage()])
                    ->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Department validation error: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Unexpected error in department creation: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()->withErrors(['message' => 'An unexpected error occurred: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $department->load(['faculty', 'head', 'programs', 'teachers']);
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $faculties = Faculty::where('status', true)->get();
        $teachers = User::whereHas('roles', function($query) {
            $query->where('name', 'teacher');
        })->get();
        
        return view('departments.edit', compact('department', 'faculties', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'code' => ['required', 'string', 'max:20', Rule::unique('departments')->ignore($department->id)],
            'faculty_id' => 'required|exists:faculties,id',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'established_date' => 'nullable|date',
            'status' => 'nullable|boolean',
        ]);

        // Generate slug if not provided
        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($department->logo) {
                Storage::delete('public/department_logos/' . $department->logo);
            }
            
            $logoFile = $request->file('logo');
            $filename = time() . '_' . $logoFile->getClientOriginalName();
            $logoFile->storeAs('public/department_logos', $filename);
            $validated['logo'] = $filename;
        }

        DB::beginTransaction();
        
        try {
            $department->update($validated);
            
            // Update or create corresponding entry in academic_structures table
            $academicStructure = \App\Models\AcademicStructure::where('code', $department->getOriginal('code'))
                ->orWhere(function($query) use ($department) {
                    $query->where('type', 'department')
                          ->whereJsonContains('metadata->department_id', $department->id);
                })
                ->first();
            
            if ($academicStructure) {
                // Update existing record
                $academicStructure->name = $department->name;
                $academicStructure->code = $department->code;
                $academicStructure->description = $department->description;
                
                // Update parent (faculty) if changed
                if ($department->faculty_id) {
                    $faculty = \App\Models\Faculty::find($department->faculty_id);
                    $parentStructure = \App\Models\AcademicStructure::where('code', $faculty->code)->first();
                    if ($parentStructure) {
                        $academicStructure->parent_id = $parentStructure->id;
                    }
                }
                
                $academicStructure->is_active = $department->status ?? true;
                $academicStructure->save();
            } else {
                // Create new record if it doesn't exist
                $academicStructure = new \App\Models\AcademicStructure();
                $academicStructure->name = $department->name;
                $academicStructure->type = 'department';
                $academicStructure->code = $department->code;
                $academicStructure->description = $department->description;
                
                // Set faculty as parent if exists
                if ($department->faculty_id) {
                    $faculty = \App\Models\Faculty::find($department->faculty_id);
                    $parentStructure = \App\Models\AcademicStructure::where('code', $faculty->code)->first();
                    if ($parentStructure) {
                        $academicStructure->parent_id = $parentStructure->id;
                    }
                }
                
                $academicStructure->is_active = $department->status ?? true;
                $academicStructure->metadata = [
                    'department_id' => $department->id,
                ];
                $academicStructure->save();
            }
            
            DB::commit();
            return redirect()->route('departments.index')
                ->with('success', 'Department updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while updating the department.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        // Check if department has programs
        if ($department->programs()->count() > 0) {
            return back()->withErrors(['message' => 'Cannot delete department with associated programs.']);
        }

        // Check if department has teachers
        if ($department->teachers()->count() > 0) {
            return back()->withErrors(['message' => 'Cannot delete department with associated teachers.']);
        }

        // Check if department has students
        if ($department->students()->count() > 0) {
            return back()->withErrors(['message' => 'Cannot delete department with associated students.']);
        }

        // Begin transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // Delete logo if exists
            if ($department->logo) {
                Storage::delete('public/department_logos/' . $department->logo);
            }
            
            // Delete corresponding entry in academic_structures table
            \App\Models\AcademicStructure::where('code', $department->code)
                ->orWhere(function($query) use ($department) {
                    $query->where('type', 'department')
                          ->whereJsonContains('metadata->department_id', $department->id);
                })
                ->delete();
            
            $department->delete();
            
            DB::commit();
            return redirect()->route('departments.index')
                ->with('success', 'Department deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while deleting the department.']);
        }
    }
    
    /**
     * Display department dashboard with statistics.
     */
    public function dashboard(Department $department)
    {
        $department->load(['faculty', 'head', 'programs', 'teachers', 'students']);
        
        // Calculate statistics
        $totalPrograms = $department->programs()->count();
        $totalTeachers = $department->teachers()->count();
        $totalStudents = $department->students()->count();
        
        $programStats = $department->programs()->withCount('students')->get();
        
        return view('departments.dashboard', compact(
            'department',
            'totalPrograms',
            'totalTeachers',
            'totalStudents',
            'programStats'
        ));
    }
    
    /**
     * Assign teachers to the department.
     */
    public function assignTeachers(Request $request, Department $department)
    {
        $validated = $request->validate([
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:users,id'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Get users who are teachers
            $teachers = User::whereIn('id', $validated['teacher_ids'])
                ->whereHas('roles', function($query) {
                    $query->where('name', 'teacher');
                })->get();
            
            // Sync teachers to department
            $department->teachers()->sync($teachers->pluck('id')->toArray());
            
            DB::commit();
            return redirect()->route('departments.show', $department)
                ->with('success', 'Teachers assigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while assigning teachers.'])
                ->withInput();
        }
    }
    
    /**
     * Remove a teacher from department.
     */
    public function removeTeacher(Department $department, User $teacher)
    {
        DB::beginTransaction();
        
        try {
            $department->teachers()->detach($teacher->id);
            
            DB::commit();
            return redirect()->route('departments.show', $department)
                ->with('success', 'Teacher removed from department successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while removing the teacher.']);
        }
    }

    /**
     * Display courses associated with the department through programs.
     */
    public function courses(Department $department)
    {
        $courses = $department->courses()->with(['program'])->paginate(15);
        return view('departments.courses', compact('department', 'courses'));
    }
}

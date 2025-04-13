<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    /**
     * Display a listing of the programs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $programs = Program::with('department')->paginate(10);
            return view('programs.index', compact('programs'));
        } catch (\Exception $e) {
            Log::error('Error fetching programs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load programs: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new program.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $departments = Department::all();
            $coordinators = User::role('teacher')->get();
            return view('programs.create', compact('departments', 'coordinators'));
        } catch (\Exception $e) {
            Log::error('Error loading program create form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load program creation form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created program in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:programs,code',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'coordinator_id' => 'nullable|exists:users,id',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:years,semesters',
            'credit_hours' => 'required|integer|min:0',
            'degree_level' => 'required|string|max:50',
            'admission_requirements' => 'nullable|string',
            'curriculum' => 'nullable|string',
            'tuition_fee' => 'nullable|numeric|min:0',
            'max_students' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create the program
            $program = new Program();
            $program->name = $request->name;
            $program->slug = Str::slug($request->name);
            $program->code = $request->code;
            $program->description = $request->description;
            $program->department_id = $request->department_id;
            $program->coordinator_id = $request->coordinator_id;
            $program->duration = $request->duration;
            $program->duration_unit = $request->duration_unit;
            $program->credit_hours = $request->credit_hours;
            $program->degree_level = $request->degree_level;
            $program->admission_requirements = $request->admission_requirements;
            $program->curriculum = $request->curriculum;
            $program->tuition_fee = $request->tuition_fee;
            $program->max_students = $request->max_students;
            $program->start_date = $request->start_date;
            $program->end_date = $request->end_date;
            $program->status = $request->status ?? true;
            $program->save();

            // Create corresponding entry in academic_structures table
            // First check if an entry with this code already exists
            $academicStructure = \App\Models\AcademicStructure::where('code', $program->code)->first();
            
            if (!$academicStructure) {
                // Get the department to set as parent
                $department = \App\Models\Department::find($program->department_id);
                
                // Find the department in academic_structures
                $parentStructure = \App\Models\AcademicStructure::where('code', $department->code)->first();
                
                // Create new academic structure
                $academicStructure = new \App\Models\AcademicStructure();
                $academicStructure->name = $program->name;
                $academicStructure->type = 'program';
                $academicStructure->code = $program->code;
                $academicStructure->description = $program->description;
                $academicStructure->parent_id = $parentStructure ? $parentStructure->id : null;
                $academicStructure->is_active = $program->status;
                
                // Store additional program data in metadata
                $academicStructure->metadata = [
                    'program_id' => $program->id,
                    'degree_level' => $program->degree_level,
                    'credit_hours' => $program->credit_hours,
                    'duration' => $program->duration,
                    'duration_unit' => $program->duration_unit
                ];
                
                $academicStructure->save();
            }

            DB::commit();
            return redirect()->route('programs.index')->with('success', 'Program created successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error creating program: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating program: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create program: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified program.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function show(Program $program)
    {
        try {
            $program->load(['department', 'coordinator', 'courses', 'students']);
            return view('programs.show', compact('program'));
        } catch (\Exception $e) {
            Log::error('Error showing program: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load program details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified program.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function edit(Program $program)
    {
        try {
            $departments = Department::all();
            $coordinators = User::role('teacher')->get();
            return view('programs.edit', compact('program', 'departments', 'coordinators'));
        } catch (\Exception $e) {
            Log::error('Error loading program edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load program edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified program in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:programs,code,' . $program->id,
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'coordinator_id' => 'nullable|exists:users,id',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:years,semesters',
            'credit_hours' => 'required|integer|min:0',
            'degree_level' => 'required|string|max:50',
            'admission_requirements' => 'nullable|string',
            'curriculum' => 'nullable|string',
            'tuition_fee' => 'nullable|numeric|min:0',
            'max_students' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Update the program
            $program->name = $request->name;
            $program->slug = Str::slug($request->name);
            $program->code = $request->code;
            $program->description = $request->description;
            $program->department_id = $request->department_id;
            $program->coordinator_id = $request->coordinator_id;
            $program->duration = $request->duration;
            $program->duration_unit = $request->duration_unit;
            $program->credit_hours = $request->credit_hours;
            $program->degree_level = $request->degree_level;
            $program->admission_requirements = $request->admission_requirements;
            $program->curriculum = $request->curriculum;
            $program->tuition_fee = $request->tuition_fee;
            $program->max_students = $request->max_students;
            $program->start_date = $request->start_date;
            $program->end_date = $request->end_date;
            $program->status = $request->status ?? true;
            $program->save();

            // Update corresponding entry in academic_structures table
            $academicStructure = \App\Models\AcademicStructure::where('code', $program->code)
                ->orWhere(function($query) use ($program) {
                    $query->where('type', 'program')
                          ->whereJsonContains('metadata->program_id', $program->id);
                })
                ->first();
            
            if ($academicStructure) {
                // Get the department to set as parent
                $department = \App\Models\Department::find($program->department_id);
                
                // Find the department in academic_structures
                $parentStructure = \App\Models\AcademicStructure::where('code', $department->code)->first();
                
                // Update academic structure
                $academicStructure->name = $program->name;
                $academicStructure->code = $program->code;
                $academicStructure->description = $program->description;
                $academicStructure->parent_id = $parentStructure ? $parentStructure->id : null;
                $academicStructure->is_active = $program->status;
                
                // Update metadata
                $metadata = $academicStructure->metadata ?? [];
                $metadata['program_id'] = $program->id;
                $metadata['degree_level'] = $program->degree_level;
                $metadata['credit_hours'] = $program->credit_hours;
                $metadata['duration'] = $program->duration;
                $metadata['duration_unit'] = $program->duration_unit;
                $academicStructure->metadata = $metadata;
                
                $academicStructure->save();
            } else {
                // Create new academic structure if it doesn't exist
                // Get the department to set as parent
                $department = \App\Models\Department::find($program->department_id);
                
                // Find the department in academic_structures
                $parentStructure = \App\Models\AcademicStructure::where('code', $department->code)->first();
                
                // Create new academic structure
                $academicStructure = new \App\Models\AcademicStructure();
                $academicStructure->name = $program->name;
                $academicStructure->type = 'program';
                $academicStructure->code = $program->code;
                $academicStructure->description = $program->description;
                $academicStructure->parent_id = $parentStructure ? $parentStructure->id : null;
                $academicStructure->is_active = $program->status;
                
                // Store additional program data in metadata
                $academicStructure->metadata = [
                    'program_id' => $program->id,
                    'degree_level' => $program->degree_level,
                    'credit_hours' => $program->credit_hours,
                    'duration' => $program->duration,
                    'duration_unit' => $program->duration_unit
                ];
                
                $academicStructure->save();
            }

            DB::commit();
            return redirect()->route('programs.show', $program)->with('success', 'Program updated successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error updating program: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating program: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update program: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified program from storage.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function destroy(Program $program)
    {
        try {
            // Check if the program has students
            if ($program->students()->count() > 0) {
                return redirect()->back()->with('error', 'Cannot delete program with enrolled students.');
            }

            DB::beginTransaction();

            // Delete corresponding entry in academic_structures table
            \App\Models\AcademicStructure::where('code', $program->code)
                ->orWhere(function($query) use ($program) {
                    $query->where('type', 'program')
                          ->whereJsonContains('metadata->program_id', $program->id);
                })
                ->delete();

            // Detach any related courses
            $program->courses()->detach();
            
            // Delete the program
            $program->delete();

            DB::commit();
            return redirect()->route('programs.index')->with('success', 'Program deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting program: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete program: ' . $e->getMessage());
        }
    }

    /**
     * Display the program dashboard.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Program $program)
    {
        try {
            $program->load(['department', 'coordinator']);
            
            // Get stats
            $totalStudents = $program->students()->count();
            $totalCourses = $program->courses()->count();
            
            // Get recent activities (this could be implemented based on your app's activity logging)
            $recentActivities = [];
            
            return view('programs.dashboard', compact('program', 'totalStudents', 'totalCourses', 'recentActivities'));
        } catch (\Exception $e) {
            Log::error('Error loading program dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load program dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Display students enrolled in the program.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function students(Program $program)
    {
        try {
            $students = $program->students()->with('user')->paginate(15);
            return view('programs.students', compact('program', 'students'));
        } catch (\Exception $e) {
            Log::error('Error loading program students: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load program students: ' . $e->getMessage());
        }
    }

    /**
     * Display courses in the program.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function courses(Program $program)
    {
        try {
            $courses = $program->courses()->paginate(15);
            return view('programs.courses', compact('program', 'courses'));
        } catch (\Exception $e) {
            Log::error('Error loading program courses: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load program courses: ' . $e->getMessage());
        }
    }
} 
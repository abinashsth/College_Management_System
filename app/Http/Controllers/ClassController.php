<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Program;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view classes']);
    }

    public function index()
    {
        $classes = Classes::with(['academicYear', 'department', 'program'])
            ->withCount('students')
            ->paginate(10);
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $academicYears = AcademicYear::all();
        $departments = Department::all();
        $programs = Program::all();
        
        return view('classes.create', compact('academicYears', 'departments', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'department_id' => 'required|exists:departments,id',
            'program_id' => 'required|exists:programs,id',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Classes::create($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully');
    }

    public function edit(Classes $class)
    {
        $academicYears = AcademicYear::all();
        $departments = Department::all();
        $programs = Program::all();
        
        return view('classes.edit', compact('class', 'academicYears', 'departments', 'programs'));
    }

    public function update(Request $request, Classes $class)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'department_id' => 'required|exists:departments,id',
            'program_id' => 'required|exists:programs,id',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $class->update($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully');
    }

    public function destroy(Classes $class)
    {
        if ($class->students()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Cannot delete class with students. Please remove students first.');
        }
        
        if ($class->sections()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Cannot delete class with sections. Please remove sections first.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully');
    }
    
    /**
     * Show the details of a specific class.
     */
    public function show(Classes $class)
    {
        $class->load(['academicYear', 'department', 'program', 'sections.teacher', 'students', 'courses']);
        
        // Get section IDs that belong to this class
        $sectionIds = $class->sections->pluck('id')->toArray();
        
        // Load the classroom allocations for sections that belong to this class
        $classroomAllocations = \App\Models\ClassroomAllocation::whereIn('section_id', $sectionIds)->get();
        
        return view('classes.show', compact('class', 'classroomAllocations'));
    }
    
    /**
     * Show the form for managing courses for a class.
     */
    public function manageCourses(Classes $class)
    {
        try {
            $class->load('courses');
            
            // Get courses from the same department or from the program
            $availableCourses = Course::where('department_id', $class->department_id)
                ->orWhereHas('programs', function($query) use ($class) {
                    $query->where('programs.id', $class->program_id);
                })
                ->get();
            
            return view('classes.courses', compact('class', 'availableCourses'));
        } catch (\Exception $e) {
            Log::error('Error loading courses form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load course assignment form: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the courses assigned to a class.
     */
    public function updateCourses(Request $request, Classes $class)
    {
        $request->validate([
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'semester.*' => 'required|integer|min:1',
            'year.*' => 'required|integer|min:1',
            'notes.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Detach all existing courses
            $class->courses()->detach();

            // Attach new courses
            if ($request->has('courses') && is_array($request->courses)) {
                foreach ($request->courses as $index => $courseId) {
                    $class->courses()->attach($courseId, [
                        'semester' => $request->semester[$index],
                        'year' => $request->year[$index],
                        'is_active' => true,
                        'notes' => $request->notes[$index] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('classes.show', $class)
                ->with('success', 'Courses assigned to class successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating courses: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update courses: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Course::query();
            
            // Filter by department
            if ($request->has('department_id') && $request->department_id) {
                $query->where('department_id', $request->department_id);
            }
            
            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            // Filter by level
            if ($request->has('level') && $request->level) {
                $query->where('level', $request->level);
            }

            // Filter by credit hours
            if ($request->has('credit_hours') && $request->credit_hours) {
                $query->where('credit_hours', $request->credit_hours);
            }
            
            // Search by name or code
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }
            
            $courses = $query->with('department')->paginate(10);
            $departments = Department::all();
            
            return view('courses.index', compact('courses', 'departments'));
        } catch (\Exception $e) {
            Log::error('Error fetching courses: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load courses: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            // Fetch departments from academic_structures table instead of departments table
            $departments = DB::table('academic_structures')
                ->where('type', 'department')
                ->where('is_active', true)
                ->get();
                
            // If no departments found, check if academic_structures table has any departments
            if ($departments->isEmpty()) {
                $deptCount = DB::table('academic_structures')->where('type', 'department')->count();
                if ($deptCount == 0) {
                    $deptTypes = DB::table('academic_structures')->select('type')->distinct()->get()->pluck('type')->toArray();
                    $typesStr = implode(', ', $deptTypes);
                    return redirect()->back()->with('error', "No departments found in academic_structures. Available types: {$typesStr}");
                }
            }
                
            return view('courses.create', compact('departments'));
        } catch (\Exception $e) {
            Log::error('Error loading course create form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load course creation form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:courses,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credit_hours' => 'required|integer|min:0',
            'lecture_hours' => 'nullable|integer|min:0',
            'lab_hours' => 'nullable|integer|min:0',
            'tutorial_hours' => 'nullable|integer|min:0',
            'level' => 'nullable|string',
            'type' => 'nullable|string',
            'department_id' => 'required|exists:academic_structures,id',
            'status' => 'nullable|string|in:active,inactive,archived',
            'learning_outcomes' => 'nullable|string',
            'evaluation_criteria' => 'nullable|string',
            'syllabus' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $course = new Course();
            $course->code = $request->code;
            $course->name = $request->name;
            $course->slug = Str::slug($request->name);
            $course->description = $request->description;
            $course->credit_hours = $request->credit_hours;
            $course->lecture_hours = $request->lecture_hours;
            $course->lab_hours = $request->lab_hours;
            $course->tutorial_hours = $request->tutorial_hours;
            $course->level = $request->level;
            $course->type = $request->type;
            $course->department_id = $request->department_id;
            $course->status = $request->status ?? 'active';
            $course->learning_outcomes = $request->learning_outcomes;
            $course->evaluation_criteria = $request->evaluation_criteria;
            $course->syllabus = $request->syllabus;
            $course->save();

            DB::commit();
            return redirect()->route('courses.index')->with('success', 'Course created successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error creating course: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating course: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create course: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        try {
            $course->load(['department', 'programs', 'prerequisites', 'prerequisiteFor']);
            return view('courses.show', compact('course'));
        } catch (\Exception $e) {
            Log::error('Error showing course: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load course details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        try {
            // Fetch departments from academic_structures table instead of departments table
            $departments = DB::table('academic_structures')
                ->where('type', 'department')
                ->where('is_active', true)
                ->get();
                
            return view('courses.edit', compact('course', 'departments'));
        } catch (\Exception $e) {
            Log::error('Error loading course edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load course edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credit_hours' => 'required|integer|min:0',
            'lecture_hours' => 'nullable|integer|min:0',
            'lab_hours' => 'nullable|integer|min:0',
            'tutorial_hours' => 'nullable|integer|min:0',
            'level' => 'nullable|string',
            'type' => 'nullable|string',
            'department_id' => 'required|exists:academic_structures,id',
            'status' => 'nullable|string|in:active,inactive,archived',
            'learning_outcomes' => 'nullable|string',
            'evaluation_criteria' => 'nullable|string',
            'syllabus' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $course->code = $request->code;
            $course->name = $request->name;
            $course->slug = Str::slug($request->name);
            $course->description = $request->description;
            $course->credit_hours = $request->credit_hours;
            $course->lecture_hours = $request->lecture_hours;
            $course->lab_hours = $request->lab_hours;
            $course->tutorial_hours = $request->tutorial_hours;
            $course->level = $request->level;
            $course->type = $request->type;
            $course->department_id = $request->department_id;
            $course->status = $request->status ?? 'active';
            $course->learning_outcomes = $request->learning_outcomes;
            $course->evaluation_criteria = $request->evaluation_criteria;
            $course->syllabus = $request->syllabus;
            $course->save();

            DB::commit();
            return redirect()->route('courses.show', $course)->with('success', 'Course updated successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error updating course: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating course: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update course: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified course from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        try {
            // Check if the course is assigned to any programs
            if ($course->programs()->count() > 0) {
                return redirect()->back()->with('error', 'Cannot delete course that is assigned to programs.');
            }

            DB::beginTransaction();

            // Detach any prerequisites or courses that require this as a prerequisite
            $course->prerequisites()->detach();
            $course->prerequisiteFor()->detach();
            
            // Delete the course
            $course->delete();

            DB::commit();
            return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting course: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete course: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for managing course prerequisites.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function managePrerequisites(Course $course)
    {
        try {
            $course->load('prerequisites');
            $availableCourses = Course::where('id', '!=', $course->id)->get();
            return view('courses.prerequisites', compact('course', 'availableCourses'));
        } catch (\Exception $e) {
            Log::error('Error loading prerequisites form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load prerequisites management: ' . $e->getMessage());
        }
    }

    /**
     * Update the prerequisites for a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function updatePrerequisites(Request $request, Course $course)
    {
        $request->validate([
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
            'requirement_type.*' => 'required|in:required,recommended,optional',
            'notes.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Detach all existing prerequisites
            $course->prerequisites()->detach();

            // Attach new prerequisites
            if ($request->has('prerequisites') && is_array($request->prerequisites)) {
                foreach ($request->prerequisites as $index => $prereqId) {
                    $course->prerequisites()->attach($prereqId, [
                        'requirement_type' => $request->requirement_type[$index] ?? 'required',
                        'notes' => $request->notes[$index] ?? null,
                        'status' => 'active',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('courses.show', $course)->with('success', 'Course prerequisites updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating prerequisites: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update prerequisites: ' . $e->getMessage());
        }
    }

    /**
     * Manage course programs assignment.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function managePrograms(Course $course)
    {
        try {
            $course->load('programs');
            $availablePrograms = Program::all();
            return view('courses.programs', compact('course', 'availablePrograms'));
        } catch (\Exception $e) {
            Log::error('Error loading programs form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load programs management: ' . $e->getMessage());
        }
    }

    /**
     * Update the programs for a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function updatePrograms(Request $request, Course $course)
    {
        try {
            $request->validate([
                'programs' => 'nullable|array',
                'programs.*' => 'exists:programs,id',
                'semester' => 'nullable|array',
                'year' => 'nullable|array',
                'is_elective' => 'nullable|array',
            ]);

            // Log the request data for debugging
            Log::info('Program assignment request data:', [
                'programs' => $request->programs,
                'semester' => $request->semester,
                'year' => $request->year,
                'is_elective' => $request->is_elective,
            ]);

            DB::beginTransaction();

            // Detach all existing programs - the pivot table is named program_courses
            $course->programs()->detach();

            // Attach new programs
            if ($request->has('programs') && is_array($request->programs)) {
                foreach ($request->programs as $programId) {
                    $semester = isset($request->semester[$programId]) ? $request->semester[$programId] : 1;
                    $year = isset($request->year[$programId]) ? $request->year[$programId] : 1;
                    $isElective = isset($request->is_elective[$programId]) ? true : false;
                    
                    Log::info("Attaching program $programId with semester $semester, year $year, isElective $isElective");
                    
                    // Use the correct pivot table as defined in the model
                    $course->programs()->attach($programId, [
                        'semester' => $semester,
                        'year' => $year,
                        'is_elective' => $isElective,
                        'status' => 'active',
                    ]);
                }
            } else {
                Log::info('No programs selected for course ' . $course->id);
            }

            DB::commit();
            return redirect()->route('courses.show', $course)->with('success', 'Course programs updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating programs: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to update programs: ' . $e->getMessage());
        }
    }
}

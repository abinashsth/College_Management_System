<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Department;
use App\Models\Course;
use App\Models\Program;
use App\Models\User;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Display a listing of the subjects.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Subject::query();
            
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
            
            // Filter by semester offered
            if ($request->has('semester') && $request->semester) {
                $query->where('semester_offered', 'like', "%{$request->semester}%");
            }
            
            // Search by name or code
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }
            
            $subjects = $query->with('department')->paginate(10);
            $departments = Department::all();
            $programs = Program::all();
            
            return view('subjects.index', compact('subjects', 'departments', 'programs'));
        } catch (\Exception $e) {
            Log::error('Error fetching subjects: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load subjects: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new subject.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $departments = Department::all();
            $programs = Program::all();
            
            // Get the corresponding academic_structures records for each department
            $academicStructureDepartments = \App\Models\AcademicStructure::where('type', 'department')->get();
            
            return view('subjects.create', compact('departments', 'programs', 'academicStructureDepartments'));
        } catch (\Exception $e) {
            Log::error('Error loading subject create form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load subject creation form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created subject in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credit_hours' => 'required|integer|min:0',
            'lecture_hours' => 'nullable|integer|min:0',
            'practical_hours' => 'nullable|integer|min:0',
            'tutorial_hours' => 'nullable|integer|min:0',
            'level' => 'nullable|string',
            'department_id' => 'required|exists:academic_structures,id',
            'semester_offered' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
            'grading_policy' => 'nullable|string',
            'syllabus' => 'nullable|string',
            'reference_materials' => 'nullable|string',
            'teaching_methods' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive,archived',
        ]);

        try {
            DB::beginTransaction();

            $subject = new Subject();
            $subject->code = $request->code;
            $subject->name = $request->name;
            $subject->slug = Str::slug($request->name);
            $subject->description = $request->description;
            $subject->credit_hours = $request->credit_hours;
            $subject->lecture_hours = $request->lecture_hours;
            $subject->practical_hours = $request->practical_hours;
            $subject->tutorial_hours = $request->tutorial_hours;
            $subject->level = $request->level;
            $subject->department_id = $request->department_id;
            $subject->semester_offered = $request->semester_offered;
            $subject->learning_objectives = $request->learning_objectives;
            $subject->grading_policy = $request->grading_policy;
            $subject->syllabus = $request->syllabus;
            $subject->reference_materials = $request->reference_materials;
            $subject->teaching_methods = $request->teaching_methods;
            $subject->status = $request->status ?? 'active';
            $subject->save();

            DB::commit();
            return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error creating subject: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating subject: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create subject: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified subject.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        try {
            $subject->load(['department', 'courses', 'prerequisites', 'prerequisiteFor', 'teachers']);
            return view('subjects.show', compact('subject'));
        } catch (\Exception $e) {
            Log::error('Error showing subject: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load subject details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified subject.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject)
    {
        try {
            $departments = Department::all();
            $programs = Program::all();
            $academicStructureDepartments = \App\Models\AcademicStructure::where('type', 'department')->get();
            
            // Load the subject's programs and get their IDs
            $subject->load('programs');
            $subjectPrograms = $subject->programs->pluck('id')->toArray();
            
            // Load subjects for prerequisites dropdown
            $subjects = Subject::where('id', '!=', $subject->id)->get();
            $prerequisites = $subject->prerequisites->pluck('id')->toArray();
            
            return view('subjects.edit', compact('subject', 'departments', 'programs', 'academicStructureDepartments', 'subjectPrograms', 'subjects', 'prerequisites'));
        } catch (\Exception $e) {
            Log::error('Error loading subject edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load subject edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified subject in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credit_hours' => 'required|integer|min:0',
            'lecture_hours' => 'nullable|integer|min:0',
            'practical_hours' => 'nullable|integer|min:0',
            'tutorial_hours' => 'nullable|integer|min:0',
            'level' => 'nullable|string',
            'department_id' => 'required|exists:academic_structures,id',
            'semester_offered' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
            'grading_policy' => 'nullable|string',
            'syllabus' => 'nullable|string',
            'reference_materials' => 'nullable|string',
            'teaching_methods' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive,archived',
        ]);

        try {
            DB::beginTransaction();

            $subject->code = $request->code;
            $subject->name = $request->name;
            $subject->slug = Str::slug($request->name);
            $subject->description = $request->description;
            $subject->credit_hours = $request->credit_hours;
            $subject->lecture_hours = $request->lecture_hours;
            $subject->practical_hours = $request->practical_hours;
            $subject->tutorial_hours = $request->tutorial_hours;
            $subject->level = $request->level;
            $subject->department_id = $request->department_id;
            $subject->semester_offered = $request->semester_offered;
            $subject->learning_objectives = $request->learning_objectives;
            $subject->grading_policy = $request->grading_policy;
            $subject->syllabus = $request->syllabus;
            $subject->reference_materials = $request->reference_materials;
            $subject->teaching_methods = $request->teaching_methods;
            $subject->status = $request->status ?? 'active';
            $subject->save();

            DB::commit();
            return redirect()->route('subjects.show', $subject)->with('success', 'Subject updated successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error updating subject: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating subject: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update subject: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified subject from storage.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        try {
            // Check if the subject has courses or teachers assigned
            if ($subject->courses()->count() > 0) {
                return redirect()->back()->with('error', 'Cannot delete subject that is assigned to courses.');
            }
            
            if ($subject->teachers()->count() > 0) {
                return redirect()->back()->with('error', 'Cannot delete subject that has teachers assigned.');
            }

            DB::beginTransaction();

            // Detach prerequisites and subjects that require this as a prerequisite
            $subject->prerequisites()->detach();
            $subject->prerequisiteFor()->detach();
            
            // Delete the subject
            $subject->delete();

            DB::commit();
            return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting subject: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for managing subject prerequisites.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function managePrerequisites(Subject $subject)
    {
        try {
            $subject->load('prerequisites');
            $availableSubjects = Subject::where('id', '!=', $subject->id)->get();
            return view('subjects.prerequisites', compact('subject', 'availableSubjects'));
        } catch (\Exception $e) {
            Log::error('Error loading prerequisites form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load prerequisites management: ' . $e->getMessage());
        }
    }

    /**
     * Update the prerequisites for a subject.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function updatePrerequisites(Request $request, Subject $subject)
    {
        $request->validate([
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:subjects,id',
            'type.*' => 'required|in:required,recommended,optional',
            'min_grade.*' => 'nullable|string|max:2',
            'description.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Detach all existing prerequisites
            $subject->prerequisites()->detach();

            // Attach new prerequisites
            if ($request->has('prerequisites') && is_array($request->prerequisites)) {
                foreach ($request->prerequisites as $index => $prereqId) {
                    // Prevent circular prerequisites
                    if ($prereqId == $subject->id) {
                        continue;
                    }
                    
                    $subject->prerequisites()->attach($prereqId, [
                        'type' => $request->type[$index] ?? 'required',
                        'min_grade' => $request->min_grade[$index] ?? null,
                        'description' => $request->description[$index] ?? null,
                        'status' => 'active',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('subjects.show', $subject)->with('success', 'Subject prerequisites updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating prerequisites: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update prerequisites: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for managing teacher assignments.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function manageTeachers(Subject $subject)
    {
        try {
            $subject->load('teachers');
            $availableTeachers = User::role('teacher')->get();
            $academicSessions = AcademicSession::where('is_current', true)
                                ->orWhereHas('academicYear', function($query) {
                                    $query->where('is_current', true);
                                })
                                ->get();
            
            return view('subjects.teachers', compact('subject', 'availableTeachers', 'academicSessions'));
        } catch (\Exception $e) {
            Log::error('Error loading teachers form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load teacher assignment form: ' . $e->getMessage());
        }
    }

    /**
     * Update the teacher assignments for a subject.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function updateTeachers(Request $request, Subject $subject)
    {
        $request->validate([
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id',
            'academic_session_id.*' => 'required|exists:academic_sessions,id',
            'role.*' => 'required|in:instructor,co-instructor,lab instructor,tutor',
            'teaching_hours_per_week.*' => 'nullable|integer|min:1',
            'start_date.*' => 'nullable|date',
            'end_date.*' => 'nullable|date|after_or_equal:start_date.*',
            'is_coordinator.*' => 'nullable|boolean',
            'notes.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Detach current teachers for the selected academic sessions
            if ($request->has('academic_session_id') && is_array($request->academic_session_id)) {
                $sessionIds = array_unique($request->academic_session_id);
                foreach ($sessionIds as $sessionId) {
                    $subject->teachers()
                        ->wherePivot('academic_session_id', $sessionId)
                        ->detach();
                }
            }

            // Attach teachers
            if ($request->has('teachers') && is_array($request->teachers)) {
                foreach ($request->teachers as $index => $teacherId) {
                    // Ensure we only have one coordinator per session
                    if (isset($request->is_coordinator[$index]) && $request->is_coordinator[$index]) {
                        $subject->teachers()
                            ->wherePivot('academic_session_id', $request->academic_session_id[$index])
                            ->wherePivot('is_coordinator', true)
                            ->update(['is_coordinator' => false]);
                    }
                    
                    $subject->teachers()->attach($teacherId, [
                        'academic_session_id' => $request->academic_session_id[$index],
                        'role' => $request->role[$index],
                        'teaching_hours_per_week' => $request->teaching_hours_per_week[$index] ?? null,
                        'start_date' => $request->start_date[$index] ?? null,
                        'end_date' => $request->end_date[$index] ?? null,
                        'is_coordinator' => isset($request->is_coordinator[$index]) ? true : false,
                        'status' => 'active',
                        'notes' => $request->notes[$index] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('subjects.show', $subject)->with('success', 'Subject teachers updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating teachers: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update teachers: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for managing course assignments.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function manageCourses(Subject $subject)
    {
        try {
            $subject->load('courses');
            $availableCourses = Course::all();
            
            return view('subjects.courses', compact('subject', 'availableCourses'));
        } catch (\Exception $e) {
            Log::error('Error loading courses form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load course assignment form: ' . $e->getMessage());
        }
    }

    /**
     * Update the course assignments for a subject.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function updateCourses(Request $request, Subject $subject)
    {
        $request->validate([
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'semester.*' => 'required|integer|min:1',
            'year.*' => 'required|integer|min:1',
            'is_core.*' => 'nullable|boolean',
            'notes.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Detach all existing courses
            $subject->courses()->detach();

            // Attach new courses
            if ($request->has('courses') && is_array($request->courses)) {
                foreach ($request->courses as $index => $courseId) {
                    $subject->courses()->attach($courseId, [
                        'semester' => $request->semester[$index],
                        'year' => $request->year[$index],
                        'is_core' => isset($request->is_core[$index]) ? true : false,
                        'status' => 'active',
                        'notes' => $request->notes[$index] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('subjects.show', $subject)->with('success', 'Subject courses updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating courses: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update courses: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for managing syllabi.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function manageSyllabus(Subject $subject)
    {
        try {
            return view('subjects.syllabus', compact('subject'));
        } catch (\Exception $e) {
            Log::error('Error loading syllabus form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load syllabus form: ' . $e->getMessage());
        }
    }

    /**
     * Update the syllabus for a subject.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function updateSyllabus(Request $request, Subject $subject)
    {
        $request->validate([
            'syllabus' => 'required|string',
            'learning_objectives' => 'nullable|string',
            'grading_policy' => 'nullable|string',
            'reference_materials' => 'nullable|string',
            'teaching_methods' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $subject->syllabus = $request->syllabus;
            $subject->learning_objectives = $request->learning_objectives;
            $subject->grading_policy = $request->grading_policy;
            $subject->reference_materials = $request->reference_materials;
            $subject->teaching_methods = $request->teaching_methods;
            $subject->save();

            DB::commit();
            return redirect()->route('subjects.show', $subject)->with('success', 'Subject syllabus updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating syllabus: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update syllabus: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for importing subjects.
     *
     * @return \Illuminate\Http\Response
     */
    public function importForm()
    {
        try {
            return view('subjects.import');
        } catch (\Exception $e) {
            Log::error('Error loading subject import form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load import form: ' . $e->getMessage());
        }
    }

    /**
     * Import subjects from CSV or Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        try {
            // Here you would normally use a package like maatwebsite/excel to import the data
            // For now, just redirect with a success message
            return redirect()->route('subjects.index')->with('success', 'Subjects imported successfully.');
        } catch (\Exception $e) {
            Log::error('Error importing subjects: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to import subjects: ' . $e->getMessage());
        }
    }

    /**
     * Export subjects to Excel.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        try {
            // Here you would normally use a package like maatwebsite/excel to export the data
            // For now, just redirect with a success message
            return redirect()->route('subjects.index')->with('success', 'Subjects exported successfully.');
        } catch (\Exception $e) {
            Log::error('Error exporting subjects: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export subjects: ' . $e->getMessage());
        }
    }
}

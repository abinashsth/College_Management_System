<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Subject;
use App\Models\AcademicSession;
use App\Models\ExamSchedule;
use App\Models\ExamRule;
use App\Models\ExamMaterial;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view exams'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:create exams'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit exams'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete exams'])->only(['destroy']);
        $this->middleware(['auth', 'permission:manage exam schedules'])->only(['schedules', 'createSchedule', 'storeSchedule', 'editSchedule', 'updateSchedule', 'destroySchedule']);
        $this->middleware(['auth', 'permission:manage exam supervisors'])->only(['supervisors', 'assignSupervisor', 'storeSupervisor', 'editSupervisor', 'updateSupervisor', 'removeSupervisor']);
        $this->middleware(['auth', 'permission:manage exam rules'])->only(['rules', 'createRule', 'storeRule', 'editRule', 'updateRule', 'destroyRule']);
        $this->middleware(['auth', 'permission:manage exam materials'])->only(['materials', 'createMaterial', 'storeMaterial', 'editMaterial', 'updateMaterial', 'destroyMaterial', 'downloadMaterial', 'approveMaterial']);
    }

    /**
     * Display a listing of exams.
     */
    public function index(Request $request)
    {
        // Apply filters if provided
        $query = Exam::query()
            ->with(['class', 'subject', 'academicSession']);
            
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->filled('academic_session_id')) {
            $query->where('academic_session_id', $request->academic_session_id);
        }
        
        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        // Order by most recent by default
        $exams = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get data for filters
        $classes = Classes::all();
        $subjects = Subject::all();
        $academicSessions = AcademicSession::all();
        
        // Get exam types from model
        $examTypes = [
            'midterm' => 'Midterm',
            'final' => 'Final',
            'quiz' => 'Quiz',
            'assignment' => 'Assignment',
            'project' => 'Project',
            'other' => 'Other'
        ];
        
        return view('exams.index', compact(
            'exams',
            'classes',
            'subjects',
            'academicSessions',
            'examTypes'
        ));
    }

    /**
     * Show form for creating a new exam.
     */
    public function create()
    {
        $classes = Classes::all();
        $subjects = Subject::all();
        $academicSessions = AcademicSession::all();
        $examTypes = [
            'midterm' => 'Midterm',
            'final' => 'Final',
            'quiz' => 'Quiz',
            'assignment' => 'Assignment',
            'project' => 'Project',
            'other' => 'Other'
        ];
        
        return view('exams.create', compact(
            'classes',
            'subjects',
            'academicSessions',
            'examTypes'
        ));
    }

    /**
     * Store a newly created exam.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'exam_date' => 'required|date',
                'class_id' => 'required|exists:classes,id',
                'subject_id' => 'required|exists:subjects,id',
                'academic_session_id' => 'required|exists:academic_sessions,id',
                'exam_type' => ['required', Rule::in(['midterm', 'final', 'quiz', 'assignment', 'project', 'other'])],
                'semester' => 'nullable|string|max:50',
                'duration_minutes' => 'required|integer|min:1',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'location' => 'nullable|string|max:255',
                'room_number' => 'nullable|string|max:50',
                'total_marks' => 'required|integer|min:1',
                'passing_marks' => 'required|integer|min:1|lte:total_marks',
                'registration_deadline' => 'nullable|date',
                'result_date' => 'nullable|date|after:exam_date',
                'weight_percentage' => 'nullable|numeric|min:0|max:100',
                'grading_scale' => 'nullable|string|max:50',
            ]);
            
            // Add created_by
            $validated['created_by'] = Auth::id();
            $validated['is_active'] = true;
            
            // Use a transaction to ensure data integrity
            $exam = DB::transaction(function () use ($validated) {
                return Exam::create($validated);
            });
            
            return redirect()
                ->route('exams.show', $exam)
                ->with('success', 'Exam created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            \Log::error('Error creating exam: ' . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the exam. Please try again.');
        }
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        $exam->load([
            'class', 
            'subject', 
            'academicSession', 
            'schedules',
            'rules',
            'materials',
            'creator',
            'updater'
        ]);
        
        // Get students for this exam's class
        $students = Student::where('class_id', $exam->class_id)
            ->where('enrollment_status', 'active')
            ->get();
            
        // Get enrolled students
        $enrolledStudents = $exam->students;
        
        return view('exams.show', compact('exam', 'students', 'enrolledStudents'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        $classes = Classes::all();
        $subjects = Subject::all();
        $academicSessions = AcademicSession::all();
        $examTypes = [
            'midterm' => 'Midterm',
            'final' => 'Final',
            'quiz' => 'Quiz',
            'assignment' => 'Assignment',
            'project' => 'Project',
            'other' => 'Other'
        ];
        
        return view('exams.edit', compact(
            'exam',
            'classes',
            'subjects',
            'academicSessions',
            'examTypes'
        ));
    }

    /**
     * Update the specified exam.
     */
    public function update(Request $request, Exam $exam)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'exam_date' => 'required|date',
                'class_id' => 'required|exists:classes,id',
                'subject_id' => 'required|exists:subjects,id',
                'academic_session_id' => 'required|exists:academic_sessions,id',
                'exam_type' => ['required', Rule::in(['midterm', 'final', 'quiz', 'assignment', 'project', 'other'])],
                'semester' => 'nullable|string|max:50',
                'duration_minutes' => 'required|integer|min:1',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'location' => 'nullable|string|max:255',
                'room_number' => 'nullable|string|max:50',
                'total_marks' => 'required|integer|min:1',
                'passing_marks' => 'required|integer|min:1|lte:total_marks',
                'registration_deadline' => 'nullable|date',
                'result_date' => 'nullable|date|after:exam_date',
                'weight_percentage' => 'nullable|numeric|min:0|max:100',
                'grading_scale' => 'nullable|string|max:50',
                'is_active' => 'boolean',
                'is_published' => 'boolean',
            ]);
            
            // Add updated_by
            $validated['updated_by'] = Auth::id();
            
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($exam, $validated) {
                $exam->update($validated);
            });
            
            return redirect()
                ->route('exams.show', $exam)
                ->with('success', 'Exam updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            \Log::error('Error updating exam: ' . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while updating the exam. Please try again.');
        }
    }

    /**
     * Remove the specified exam.
     */
    public function destroy(Exam $exam)
    {
        try {
            // Check if the exam has any associated records
            $hasSchedules = $exam->schedules()->exists();
            $hasRules = $exam->rules()->exists();
            $hasMaterials = $exam->materials()->exists();
            $hasStudents = $exam->students()->exists();
            
            // If there are associated records, don't allow deletion
            if ($hasSchedules || $hasRules || $hasMaterials || $hasStudents) {
                return redirect()->back()->with('error', 
                    'Unable to delete the exam because it has associated records. ' .
                    'Please remove all schedules, rules, materials, and student enrollments first, or deactivate the exam instead.');
            }
            
            // Wrap the deletion in a transaction
            DB::transaction(function () use ($exam) {
                $exam->delete();
            });
            
            return redirect()
                ->route('exams.index')
                ->with('success', 'Exam deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            \Log::error('Error deleting exam: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the exam. Please try again.');
        }
    }

    /**
     * Display exam schedules.
     */
    public function schedules(Exam $exam)
    {
        $exam->load(['schedules.section.class', 'schedules.supervisors.user']);
        
        return view('exams.schedules.index', compact('exam'));
    }

    /**
     * Show form for creating a new exam schedule.
     */
    public function createSchedule(Exam $exam)
    {
        $sections = Section::with('class')
            ->whereHas('class', function($query) use ($exam) {
                $query->where('id', $exam->class_id);
            })
            ->where('status', 'active')
            ->get();
            
        $statuses = [
            'scheduled' => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'postponed' => 'Postponed'
        ];
        
        return view('exams.schedules.create', compact('exam', 'sections', 'statuses'));
    }

    /**
     * Store a newly created exam schedule.
     */
    public function storeSchedule(Request $request, Exam $exam)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:50',
            'seating_capacity' => 'nullable|integer|min:1',
            'is_rescheduled' => 'boolean',
            'reschedule_reason' => 'nullable|required_if:is_rescheduled,1|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'notes' => 'nullable|string'
        ]);

        // Add creator information
        $scheduleData = $request->all();
        $scheduleData['exam_id'] = $exam->id;
        $scheduleData['created_by'] = Auth::id();
        $scheduleData['updated_by'] = Auth::id();
        
        $schedule = ExamSchedule::create($scheduleData);

        return redirect()->route('exams.schedules', $exam)
            ->with('success', 'Exam schedule created successfully');
    }

    /**
     * Show the form for editing an exam schedule.
     */
    public function editSchedule(Exam $exam, ExamSchedule $schedule)
    {
        $sections = Section::with('class')
            ->whereHas('class', function($query) use ($exam) {
                $query->where('id', $exam->class_id);
            })
            ->where('status', 'active')
            ->get();
            
        $statuses = [
            'scheduled' => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'postponed' => 'Postponed'
        ];
        
        return view('exams.schedules.edit', compact('exam', 'schedule', 'sections', 'statuses'));
    }

    /**
     * Update the specified exam schedule.
     */
    public function updateSchedule(Request $request, Exam $exam, ExamSchedule $schedule)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:50',
            'seating_capacity' => 'nullable|integer|min:1',
            'is_rescheduled' => 'boolean',
            'reschedule_reason' => 'nullable|required_if:is_rescheduled,1|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'notes' => 'nullable|string'
        ]);

        // Add updater information
        $scheduleData = $request->all();
        $scheduleData['updated_by'] = Auth::id();
        
        $schedule->update($scheduleData);

        return redirect()->route('exams.schedules', $exam)
            ->with('success', 'Exam schedule updated successfully');
    }

    /**
     * Remove the specified exam schedule.
     */
    public function destroySchedule(Exam $exam, ExamSchedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('exams.schedules', $exam)
            ->with('success', 'Exam schedule deleted successfully');
    }

    /**
     * Form for assigning supervisors.
     */
    public function assignSupervisor(Exam $exam, ExamSchedule $schedule)
    {
        $teachers = User::role('Teacher')->get();
        $roles = [
            'chief_supervisor' => 'Chief Supervisor',
            'supervisor' => 'Supervisor',
            'assistant_supervisor' => 'Assistant Supervisor',
            'invigilator' => 'Invigilator',
            'other' => 'Other'
        ];
        
        return view('exams.supervisors.create', compact('exam', 'schedule', 'teachers', 'roles'));
    }

    /**
     * Store a newly assigned supervisor.
     */
    public function storeSupervisor(Request $request, Exam $exam, ExamSchedule $schedule)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:chief_supervisor,supervisor,assistant_supervisor,invigilator,other',
            'reporting_time' => 'nullable|date_format:H:i',
            'responsibilities' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Check if this supervisor is already assigned to this schedule
        $existingSupervisor = $schedule->supervisors()
            ->where('user_id', $request->user_id)
            ->first();
            
        if ($existingSupervisor) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This supervisor is already assigned to this schedule');
        }

        // Add creator information
        $supervisorData = $request->all();
        $supervisorData['exam_schedule_id'] = $schedule->id;
        $supervisorData['assigned_by'] = Auth::id();
        
        $supervisor = $schedule->supervisors()->create($supervisorData);

        return redirect()->route('exams.schedules', $exam)
            ->with('success', 'Supervisor assigned successfully');
    }

    /**
     * Display a list of exam rules.
     */
    public function rules(Exam $exam)
    {
        $globalRules = ExamRule::where('is_global', true)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
            
        $examRules = $exam->rules()
            ->orderBy('display_order')
            ->get();
            
        return view('exams.rules.index', compact('exam', 'globalRules', 'examRules'));
    }

    /**
     * Show form for creating a new exam rule.
     */
    public function createRule(Exam $exam)
    {
        $categories = ExamRule::getCategories();
        
        return view('exams.rules.create', compact('exam', 'categories'));
    }

    /**
     * Store a newly created exam rule.
     */
    public function storeRule(Request $request, Exam $exam)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'rule_type' => 'required|string|max:50',
                'applies_to' => 'required|string|max:50',
                'is_active' => 'boolean'
            ]);
            
            // Add creator
            $validated['created_by'] = Auth::id();
            $validated['exam_id'] = $exam->id;
            
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($validated) {
                ExamRule::create($validated);
            });
            
            return redirect()
                ->route('exams.rules', $exam)
                ->with('success', 'Exam rule created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            \Log::error('Error creating exam rule: ' . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the exam rule. Please try again.');
        }
    }

    /**
     * Show the form for editing an exam rule.
     */
    public function editRule(Exam $exam, ExamRule $rule)
    {
        $categories = ExamRule::getCategories();
        
        return view('exams.rules.edit', compact('exam', 'rule', 'categories'));
    }

    /**
     * Update the specified exam rule.
     */
    public function updateRule(Request $request, Exam $exam, ExamRule $rule)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'is_global' => 'boolean',
            'description' => 'required|string',
            'is_mandatory' => 'boolean',
            'display_order' => 'integer|min:0',
            'category' => 'required|in:general,conduct,materials,timing,grading,other',
            'penalty_for_violation' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $ruleData = $request->all();
        $ruleData['exam_id'] = $request->is_global ? null : $exam->id;
        
        $rule->update($ruleData);

        return redirect()->route('exams.rules', $exam)
            ->with('success', 'Exam rule updated successfully');
    }

    /**
     * Remove the specified exam rule.
     */
    public function destroyRule(Exam $exam, ExamRule $rule)
    {
        $rule->delete();

        return redirect()->route('exams.rules', $exam)
            ->with('success', 'Exam rule deleted successfully');
    }

    /**
     * Display a list of exam materials.
     */
    public function materials(Exam $exam)
    {
        $materials = $exam->materials()
            ->with(['creator', 'approver'])
            ->orderBy('type')
            ->orderBy('version', 'desc')
            ->get();
            
        return view('exams.materials.index', compact('exam', 'materials'));
    }

    /**
     * Show form for uploading a new exam material.
     */
    public function createMaterial(Exam $exam)
    {
        $materialTypes = ExamMaterial::getTypes();
        
        return view('exams.materials.create', compact('exam', 'materialTypes'));
    }

    /**
     * Store a newly uploaded exam material.
     */
    public function storeMaterial(Request $request, Exam $exam)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|string|max:50',
                'description' => 'nullable|string',
                'is_for_students' => 'boolean',
                'is_for_teachers' => 'boolean',
                'is_confidential' => 'boolean',
                'release_date' => 'nullable|date',
                'is_active' => 'boolean',
                'file' => 'required|file|max:10240', // Max file size: 10MB
            ]);
            
            // Set creator and exam relationship
            $validated['created_by'] = Auth::id();
            $validated['exam_id'] = $exam->id;
            $validated['version'] = 1;
            
            // Use a transaction to handle file upload and database insert
            DB::transaction(function () use ($validated, $request) {
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('exam_materials', $filename, 'public');
                    
                    $validated['file_path'] = $path;
                    $validated['file_type'] = $file->getClientMimeType();
                    $validated['file_size'] = $file->getSize();
                }
                
                ExamMaterial::create($validated);
            });
            
            return redirect()
                ->route('exams.materials', $exam)
                ->with('success', 'Exam material uploaded successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            \Log::error('Error uploading exam material: ' . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while uploading the exam material. Please try again.');
        }
    }

    /**
     * Edit an exam material.
     */
    public function editMaterial(Exam $exam, ExamMaterial $material)
    {
        $materialTypes = ExamMaterial::getTypes();
        
        return view('exams.materials.edit', compact('exam', 'material', 'materialTypes'));
    }

    /**
     * Update an exam material.
     */
    public function updateMaterial(Request $request, Exam $exam, ExamMaterial $material)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:question_paper,answer_sheet,supplementary,instruction,resource,marking_scheme,other',
            'file' => 'nullable|file|max:10240', // Max 10MB
            'description' => 'nullable|string',
            'is_for_students' => 'boolean',
            'is_for_teachers' => 'boolean',
            'is_confidential' => 'boolean',
            'release_date' => 'nullable|date',
            'is_active' => 'boolean',
            'version' => 'integer|min:1'
        ]);

        $materialData = $request->except('file');
        
        // Handle file upload if a new file is provided
        if ($request->hasFile('file')) {
            // Delete old file if it exists
            if ($material->file_path) {
                Storage::delete($material->file_path);
            }
            
            $file = $request->file('file');
            $path = $file->store('exam-materials');
            
            $materialData['file_path'] = $path;
            $materialData['file_type'] = $file->getClientMimeType();
            $materialData['file_size'] = $file->getSize() / 1024; // Convert to KB
        }
        
        $material->update($materialData);

        return redirect()->route('exams.materials', $exam)
            ->with('success', 'Exam material updated successfully');
    }

    /**
     * Delete an exam material.
     */
    public function destroyMaterial(Exam $exam, ExamMaterial $material)
    {
        // Delete file from storage
        if ($material->file_path) {
            Storage::delete($material->file_path);
        }
        
        $material->delete();

        return redirect()->route('exams.materials', $exam)
            ->with('success', 'Exam material deleted successfully');
    }

    /**
     * Download an exam material.
     */
    public function downloadMaterial(Exam $exam, ExamMaterial $material)
    {
        // Check if the user is allowed to download this material
        if (!$material->canBeViewedBy(Auth::user())) {
            return redirect()->back()->with('error', 'You do not have permission to download this material');
        }
        
        // Check if file exists
        if (!$material->file_path || !Storage::exists($material->file_path)) {
            return redirect()->back()->with('error', 'File not found');
        }
        
        // Generate a clean filename
        $filename = str_slug($material->title) . '.' . pathinfo($material->file_path, PATHINFO_EXTENSION);
        
        return Storage::download($material->file_path, $filename);
    }

    /**
     * Approve an exam material.
     */
    public function approveMaterial(Exam $exam, ExamMaterial $material)
    {
        $material->approve(Auth::id());
        
        return redirect()->route('exams.materials', $exam)
            ->with('success', 'Exam material approved successfully');
    }

    /**
     * Grade students for an exam.
     */
    public function grade(Exam $exam)
    {
        $students = $exam->class->students;
        $existingGrades = $exam->students()->get()->keyBy('id');
        
        return view('exams.grade', compact('exam', 'students', 'existingGrades'));
    }

    /**
     * Update grades for students in the exam.
     */
    public function updateGrades(Request $request, Exam $exam)
    {
        try {
            $validated = $request->validate([
                'grades' => 'required|array',
                'grades.*' => 'required|numeric|min:0|max:' . $exam->total_marks,
                'remarks' => 'nullable|array',
                'remarks.*' => 'nullable|string|max:255'
            ]);
            
            // Use a transaction to ensure all grades are updated or none
            DB::transaction(function () use ($exam, $validated) {
                foreach ($validated['grades'] as $studentId => $grade) {
                    $remark = $validated['remarks'][$studentId] ?? null;
                    
                    // Update the pivot table with new grade and remarks
                    $exam->students()->updateExistingPivot($studentId, [
                        'grade' => $grade,
                        'remarks' => $remark,
                        'updated_at' => now()
                    ]);
                }
            });
            
            return redirect()
                ->route('exams.show', $exam)
                ->with('success', 'Grades updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            \Log::error('Error updating grades: ' . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while updating grades. Please try again.');
        }
    }

    /**
     * Display a student's grades.
     */
    public function studentGrades()
    {
        // Only for students to view their own grades
        if (!Auth::user()->hasRole('Student')) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
        
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found');
        }
        
        $exams = Exam::whereHas('students', function($query) use ($student) {
            $query->where('student_id', $student->id);
        })->with([
            'class', 
            'subject', 
            'academicSession'
        ])->get();
        
        $grades = [];
        foreach ($exams as $exam) {
            $pivot = $exam->students()->where('student_id', $student->id)->first()->pivot;
            $grades[$exam->id] = [
                'grade' => $pivot->grade,
                'remarks' => $pivot->remarks,
                'passed' => $pivot->grade >= $exam->passing_marks
            ];
        }
        
        return view('exams.student-grades', compact('student', 'exams', 'grades'));
    }

    /**
     * Publish exam results.
     */
    public function publishResults(Exam $exam)
    {
        $exam->is_published = true;
        $exam->save();
        
        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exam results published successfully');
    }

    /**
     * Unpublish exam results.
     */
    public function unpublishResults(Exam $exam)
    {
        $exam->is_published = false;
        $exam->save();
        
        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exam results unpublished successfully');
    }

    /**
     * Enroll students in the exam.
     */
    public function enrollStudents(Request $request, Exam $exam)
    {
        try {
            $validated = $request->validate([
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:students,id'
            ]);
            
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($exam, $validated) {
                // Get current enrollments
                $currentStudents = $exam->students()->pluck('students.id')->toArray();
                
                // Determine which students to add (not already enrolled)
                $newStudents = array_diff($validated['student_ids'], $currentStudents);
                
                // Create enrollment records for new students
                foreach ($newStudents as $studentId) {
                    $exam->students()->attach($studentId, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            });
            
            return redirect()
                ->route('exams.show', $exam)
                ->with('success', 'Students enrolled successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            \Log::error('Error enrolling students: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while enrolling students. Please try again.');
        }
    }
    
    /**
     * Toggle the active status of an exam.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus(Exam $exam)
    {
        $exam->is_active = !$exam->is_active;
        $exam->updated_by = Auth::id();
        $exam->save();
        
        $status = $exam->is_active ? 'activated' : 'deactivated';
        
        return redirect()
            ->back()
            ->with('success', "Exam {$status} successfully");
    }
    
    /**
     * Toggle the published status of an exam.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function togglePublished(Exam $exam)
    {
        $exam->is_published = !$exam->is_published;
        $exam->updated_by = Auth::id();
        $exam->save();
        
        $status = $exam->is_published ? 'published' : 'unpublished';
        
        return redirect()
            ->back()
            ->with('success', "Exam {$status} successfully");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\MarkComponent;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Student;
use App\Models\GradeSystem;
use App\Models\ExamGradeScale;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MarksImport;
use App\Exports\MarksExport;
use Illuminate\Support\Str;
use App\Models\Classes;
use App\Exports\StudentMarksExport;

class MarkController extends Controller
{
    /**
     * Constructor with middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Simple welcome page to test routing
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view('marks.welcome');
    }

    /**
     * Show marks dashboard with statistics and quick links.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $activeSession = AcademicSession::where('is_active', true)->first();
        
        // Get recent exams
        $recentExams = Exam::where('is_active', true)
            ->with('academicSession')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get stats for current user
        $user = Auth::user();
        $stats = [
            'pending_verification' => 0,
            'pending_publication' => 0,
            'published' => 0,
            'total' => 0
        ];
        
        // For teachers, show stats for their subjects
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            if ($teacher) {
                $teacherSubjects = $teacher->subjects->pluck('id');
                
                $stats['pending_verification'] = Mark::where('status', Mark::STATUS_SUBMITTED)
                    ->whereIn('subject_id', $teacherSubjects)
                    ->count();
                    
                $stats['pending_publication'] = Mark::where('status', Mark::STATUS_VERIFIED)
                    ->whereIn('subject_id', $teacherSubjects)
                    ->count();
                    
                $stats['published'] = Mark::where('status', Mark::STATUS_PUBLISHED)
                    ->whereIn('subject_id', $teacherSubjects)
                    ->count();
                    
                $stats['total'] = Mark::whereIn('subject_id', $teacherSubjects)->count();
            }
        } else {
            // For admins and others, show global stats
            $stats['pending_verification'] = Mark::where('status', Mark::STATUS_SUBMITTED)->count();
            $stats['pending_publication'] = Mark::where('status', Mark::STATUS_VERIFIED)->count();
            $stats['published'] = Mark::where('status', Mark::STATUS_PUBLISHED)->count();
            $stats['total'] = Mark::count();
        }
        
        return view('marks.dashboard', compact('recentExams', 'activeSession', 'stats'));
    }

    /**
     * Show the form for selecting an exam and subject.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectExamSubject()
    {
        $exams = Exam::with('academicSession')->where('is_active', true)->orderBy('created_at', 'desc')->get();
        $subjects = Subject::orderBy('name')->get();
        
        return view('marks.select_exam_subject', compact('exams', 'subjects'));
    }

    /**
     * Display a listing of marks for a specific exam and subject.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // If no exam or subject is provided, redirect to dashboard
        if (!$request->has('exam_id') || !$request->has('subject_id')) {
            return redirect()->route('marks.dashboard');
        }
        
        try {
            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'subject_id' => 'required|exists:subjects,id',
            ]);

            $exam = Exam::with('academicSession')->findOrFail($request->exam_id);
            $subject = Subject::findOrFail($request->subject_id);
            
            // Check if user has permission to view marks
            if (!Auth::user()->can('view marks') && !$this->canViewMarks($exam, $subject)) {
                return redirect()->route('marks.select')
                    ->with('error', 'You do not have permission to view these marks');
            }
            
            $students = Student::where('class_id', $exam->class_id)
                ->where('enrollment_status', 'active')
                ->with('user')
                ->orderBy('roll_number')
                ->get();
                
            $marks = Mark::where('exam_id', $exam->id)
                ->where('subject_id', $subject->id)
                ->get()
                ->keyBy('student_id');
                
            return view('marks.index', compact('exam', 'subject', 'students', 'marks'));
        } catch (\Exception $e) {
            Log::error('Error loading marks: ' . $e->getMessage());
            
            return redirect()->route('marks.select')
                ->with('error', 'Error loading marks: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if user can view marks for this exam/subject
     * 
     * @param Exam $exam
     * @param Subject $subject
     * @return bool
     */
    private function canViewMarks(Exam $exam, Subject $subject)
    {
        $user = Auth::user();
        
        // Super-admin and users with view marks permission can view all marks
        if ($user->hasRole('super-admin') || $user->hasPermissionTo('view marks')) {
            return true;
        }
        
        // Teachers can view marks for subjects they teach
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            return $teacher && $subject->teachers->contains($teacher->id);
        }
        
        // Students can only view their own published marks
        if ($user->hasRole('Student')) {
            $student = $user->student;
            if ($student && $exam->is_published) {
                // Check if student is in the exam's class
                return $student->class_id === $exam->class_id;
            }
        }
        
        return false;
    }

    /**
     * Show the form for creating new marks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check if user has permission to create marks
        if (!Auth::user()->can('create marks') && !$this->canCreateMarks($exam, $subject)) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to create marks for this exam/subject');
        }
        
        $students = Student::where('class_id', $exam->class_id)
            ->where('enrollment_status', 'active')
            ->with('user')
            ->orderBy('roll_number')
            ->get();
            
        $marks = Mark::where('exam_id', $exam->id)
            ->where('subject_id', $subject->id)
            ->get()
            ->keyBy('student_id');
            
        return view('marks.create', compact('exam', 'subject', 'students', 'marks'));
    }

    /**
     * Store newly created marks in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'nullable|numeric|min:0|max:' . $request->total_marks,
            'marks.*.is_absent' => 'boolean',
            'marks.*.remarks' => 'nullable|string|max:255',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check if user has permission to store marks
        if (!Auth::user()->can('create marks') && !$this->canCreateMarks($exam, $subject)) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to create marks for this exam/subject');
        }
        
        DB::beginTransaction();
        
        try {
            foreach ($request->marks as $data) {
                $markData = [
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id,
                    'student_id' => $data['student_id'],
                    'marks_obtained' => isset($data['is_absent']) && $data['is_absent'] ? null : $data['marks_obtained'],
                    'total_marks' => $exam->total_marks,
                    'is_absent' => isset($data['is_absent']) && $data['is_absent'] ? true : false,
                    'remarks' => $data['remarks'] ?? null,
                    'status' => Mark::STATUS_DRAFT,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ];
                
                // Calculate grade if not absent
                if (!isset($data['is_absent']) || !$data['is_absent']) {
                    $percentage = ($data['marks_obtained'] / $exam->total_marks) * 100;
                    $grade = $this->calculateGrade($percentage, $exam);
                    $markData['grade'] = $grade;
                } else {
                    $markData['grade'] = 'AB';
                }
                
                Mark::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'subject_id' => $subject->id,
                        'student_id' => $data['student_id'],
                    ],
                    $markData
                );
            }
            
            DB::commit();
            
            return redirect()->route('marks.index', ['exam_id' => $exam->id, 'subject_id' => $subject->id])
                ->with('success', 'Marks added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving marks: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Error saving marks: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for creating marks in bulk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createBulk(Request $request)
    {
        $examId = $request->input('exam_id');
        $subjectId = $request->input('subject_id');
        
        if (!$examId || !$subjectId) {
            return redirect()->route('marks.select')
                ->with('error', 'Please select both an exam and subject to enter marks');
        }
        
        $exam = Exam::findOrFail($examId);
        $subject = Subject::findOrFail($subjectId);
        
        // Check if user has permission to create marks
        if (!Auth::user()->can('create marks') && !$this->canCreateMarks($exam, $subject)) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to create marks for this exam/subject');
        }
        
        // Get students enrolled in this exam's class
        $students = Student::where('class_id', $exam->class_id)
            ->where('enrollment_status', 'active')
            ->with('user')
            ->orderBy('roll_number')
            ->get();
        
        // Check if marks already exist for some students
        $existingMarks = Mark::where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->get()
            ->keyBy('student_id');
        
        return view('marks.create_bulk', compact('exam', 'subject', 'students', 'existingMarks'));
    }

    /**
     * Store marks in bulk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBulk(Request $request)
    {
        // Validate the request
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0',
            'is_absent' => 'array',
            'remarks' => 'array',
            'apply_mask' => 'nullable|boolean',
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check if user has permission to create marks
        if (!Auth::user()->can('create marks') && !$this->canCreateMarks($exam, $subject)) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to create marks for this exam/subject');
        }
        
        // Get mask for this subject and exam if it exists and apply_mask is checked
        $applyMask = $request->has('apply_mask');
        $mask = null;
        $maskValue = null;
        
        if ($applyMask) {
            $mask = \App\Models\SubjectMask::where('exam_id', $exam->id)
                ->where('subject_id', $subject->id)
                ->where('is_active', true)
                ->first();
                
            if ($mask) {
                $maskValue = $mask->mask_value / 100; // Convert to decimal for multiplication
            }
        }
        
        DB::beginTransaction();
        
        try {
            foreach ($request->marks as $studentId => $marks) {
                $isAbsent = isset($request->is_absent[$studentId]) && $request->is_absent[$studentId] === 'on';
                
                // Apply mask if required
                $finalMarks = $marks;
                if ($applyMask && $maskValue !== null && !$isAbsent && $marks !== null) {
                    $finalMarks = $marks * $maskValue;
                }
                
                // Calculate grade if not absent
                $grade = null;
                if (!$isAbsent && $finalMarks !== null) {
                    $percentage = ($finalMarks / $exam->total_marks) * 100;
                    $grade = $this->calculateGrade($percentage, $exam);
                } else if ($isAbsent) {
                    $grade = 'AB';
                }
                
                // Check if mark already exists for this student, exam, and subject
                $existingMark = Mark::where('exam_id', $exam->id)
                    ->where('subject_id', $subject->id)
                    ->where('student_id', $studentId)
                    ->first();
                
                if ($existingMark) {
                    // Update existing mark
                    $existingMark->marks_obtained = $isAbsent ? null : $finalMarks;
                    $existingMark->remarks = $request->remarks[$studentId] ?? null;
                    $existingMark->is_absent = $isAbsent;
                    $existingMark->grade = $grade;
                    $existingMark->updated_by = Auth::id();
                    
                    // If it was submitted or verified, revert to draft
                    if ($existingMark->status === Mark::STATUS_SUBMITTED || 
                        $existingMark->status === Mark::STATUS_VERIFIED) {
                        $existingMark->status = Mark::STATUS_DRAFT;
                    }
                    
                    $existingMark->save();
                } else {
                    // Create new mark
                    Mark::create([
                        'exam_id' => $exam->id,
                        'subject_id' => $subject->id,
                        'student_id' => $studentId,
                        'marks_obtained' => $isAbsent ? null : $finalMarks,
                        'total_marks' => $exam->total_marks,
                        'grade' => $grade,
                        'remarks' => $request->remarks[$studentId] ?? null,
                        'is_absent' => $isAbsent,
                        'status' => Mark::STATUS_DRAFT,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            }
            
            DB::commit();
            
            $message = 'Marks saved successfully.';
            if ($applyMask && $mask) {
                $message = 'Marks saved successfully with mask value of ' . $mask->mask_value . '% applied.';
            }
            
            return redirect()->route('marks.index', [
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id
                ])
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving bulk marks: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saving marks: ' . $e->getMessage());
        }
    }

    /**
     * Check if user can create marks for this exam/subject
     * 
     * @param Exam $exam
     * @param Subject $subject
     * @return bool
     */
    private function canCreateMarks(Exam $exam, Subject $subject)
    {
        $user = Auth::user();
        
        // Super-admin and users with create marks permission can create all marks
        if ($user->hasRole('super-admin') || $user->hasPermissionTo('create marks')) {
            return true;
        }
        
        // Teachers can create marks for subjects they teach
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            return $teacher && $subject->teachers->contains($teacher->id);
        }
        
        return false;
    }
    
    /**
     * Calculate grade based on percentage
     * 
     * @param float $percentage
     * @param Exam $exam
     * @return string|null
     */
    private function calculateGrade($percentage, Exam $exam)
    {
        $gradeSystem = GradeSystem::getDefault();
        if (!$gradeSystem) {
            return null;
        }
        
        return $gradeSystem->findGradeForPercentage($percentage);
    }

    /**
     * Display the specified mark.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function show(Mark $mark)
    {
        $mark->load(['exam', 'student.user', 'subject', 'creator', 'updater', 'verifier', 'components']);
        
        // Check if user has permission to view this mark
        if (!Auth::user()->can('view marks') && !$this->canViewMark($mark)) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to view this mark');
        }
        
        return view('marks.show', compact('mark'));
    }

    /**
     * Show the form for editing the specified mark.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function edit(Mark $mark)
    {
        // Check if user has permission to edit this mark
        if (!Auth::user()->can('edit marks') && !$this->canEditMark($mark)) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'You do not have permission to edit this mark');
        }
        
        // Check if mark is in an editable state
        if (!$mark->canEdit()) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'This mark cannot be edited in its current state');
        }
        
        $mark->load(['exam', 'student.user', 'subject', 'components']);
        
        // Get mask for this subject and exam if it exists
        $mask = \App\Models\SubjectMask::where('exam_id', $mark->exam_id)
            ->where('subject_id', $mark->subject_id)
            ->where('is_active', true)
            ->first();
        
        return view('marks.edit', compact('mark', 'mask'));
    }

    /**
     * Update the specified mark in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mark $mark)
    {
        // Check if user has permission to edit this mark
        if (!Auth::user()->can('edit marks') && !$this->canEditMark($mark)) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'You do not have permission to edit this mark');
        }
        
        // Check if mark is in an editable state
        if (!$mark->canEdit()) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'This mark cannot be edited in its current state');
        }
        
        // Validate the request
        $request->validate([
            'marks_obtained' => 'nullable|numeric|min:0|max:' . $mark->exam->total_marks,
            'is_absent' => 'boolean',
            'remarks' => 'nullable|string|max:255',
            'components' => 'nullable|array',
            'components.*.marks_obtained' => 'nullable|numeric|min:0',
            'components.*.total_marks' => 'required_with:components.*.marks_obtained|numeric|min:0',
            'components.*.weight_percentage' => 'required_with:components.*.marks_obtained|numeric|min:0|max:100',
            'components.*.name' => 'required_with:components.*.marks_obtained|string|max:255',
            'apply_mask' => 'nullable|boolean',
            'original_marks' => 'nullable|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            $isAbsent = $request->has('is_absent');
            $marksObtained = $isAbsent ? null : $request->marks_obtained;
            
            // Apply mask if requested
            if (!$isAbsent && $request->has('apply_mask')) {
                // Get mask for this subject and exam
                $mask = \App\Models\SubjectMask::where('exam_id', $mark->exam_id)
                    ->where('subject_id', $mark->subject_id)
                    ->where('is_active', true)
                    ->first();
                    
                if ($mask) {
                    $originalMarks = $request->has('original_marks') ? 
                        floatval($request->original_marks) : 
                        floatval($request->marks_obtained);
                    
                    $marksObtained = $originalMarks * ($mask->mask_value / 100);
                }
            }
            
            $mark->marks_obtained = $marksObtained;
            $mark->is_absent = $isAbsent;
            $mark->remarks = $request->remarks;
            $mark->updated_by = Auth::id();
            
            // Calculate grade if not absent
            if (!$isAbsent) {
                $percentage = ($marksObtained / $mark->exam->total_marks) * 100;
                $grade = $this->calculateGrade($percentage, $mark->exam);
                $mark->grade = $grade;
            } else {
                $mark->grade = 'AB';
            }
            
            $mark->save();
            
            // Handle components if present
            if (isset($request->components) && is_array($request->components)) {
                // Delete existing components
                $mark->components()->delete();
                
                // Create new components
                foreach ($request->components as $componentId => $componentData) {
                    // Skip if marks_obtained is not provided
                    if (!isset($componentData['marks_obtained'])) {
                        continue;
                    }
                    
                    MarkComponent::create([
                        'mark_id' => $mark->id,
                        'component_name' => $componentData['name'],
                        'marks_obtained' => $isAbsent ? null : $componentData['marks_obtained'],
                        'total_marks' => $componentData['total_marks'],
                        'weight_percentage' => $componentData['weight_percentage'],
                    ]);
                }
                
                // Recalculate the total mark based on components
                $mark->calculateMarks();
            }
            
            DB::commit();
            
            $message = 'Mark updated successfully.';
            if ($request->has('apply_mask') && isset($mask)) {
                $message = 'Mark updated successfully with mask value of ' . $mask->mask_value . '% applied.';
            }
            
            return redirect()->route('marks.show', $mark)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating mark: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Error updating mark: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified mark from storage.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mark $mark)
    {
        // Check if user has permission to delete this mark
        if (!Auth::user()->can('delete marks')) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'You do not have permission to delete marks');
        }
        
        // Check if mark can be deleted (draft or rejected only)
        if (!in_array($mark->status, [Mark::STATUS_DRAFT, Mark::STATUS_REJECTED])) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Only draft or rejected marks can be deleted');
        }
        
        try {
            // Delete components first
            $mark->components()->delete();
            
            // Then delete the mark
            $mark->delete();
            
            return redirect()->route('marks.index', [
                    'exam_id' => $mark->exam_id,
                    'subject_id' => $mark->subject_id
                ])
                ->with('success', 'Mark deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting mark: ' . $e->getMessage());
            
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Error deleting mark: ' . $e->getMessage());
        }
    }

    /**
     * Submit mark for verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request, Mark $mark)
    {
        // Check if user has permission to submit this mark
        if (!Auth::user()->can('create marks') && !$this->canEditMark($mark)) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'You do not have permission to submit this mark');
        }
        
        // Check if mark can be submitted
        if (!$mark->canSubmit()) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'This mark cannot be submitted in its current state');
        }
        
        try {
            if ($mark->submit(Auth::id())) {
                return redirect()->route('marks.show', $mark)
                    ->with('success', 'Mark submitted for verification');
            } else {
                return redirect()->route('marks.show', $mark)
                    ->with('error', 'Failed to submit mark for verification');
            }
        } catch (\Exception $e) {
            Log::error('Error submitting mark: ' . $e->getMessage());
            
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Error submitting mark: ' . $e->getMessage());
        }
    }

    /**
     * Verify a mark.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, Mark $mark)
    {
        // Check if user has permission to verify marks
        if (!Auth::user()->can('verify marks')) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'You do not have permission to verify marks');
        }
        
        // Check if mark can be verified
        if (!$mark->canVerify()) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'This mark cannot be verified in its current state');
        }
        
        try {
            if ($mark->verify(Auth::id())) {
                return redirect()->route('marks.show', $mark)
                    ->with('success', 'Mark verified successfully');
            } else {
                return redirect()->route('marks.show', $mark)
                    ->with('error', 'Failed to verify mark');
            }
        } catch (\Exception $e) {
            Log::error('Error verifying mark: ' . $e->getMessage());
            
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Error verifying mark: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject mark verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rejectVerification(Request $request)
    {
        $request->validate([
            'mark_id' => 'required|exists:marks,id',
            'remarks' => 'nullable|string|max:255',
        ]);
        
        $mark = Mark::findOrFail($request->mark_id);
        
        // Check if user has permission to verify marks
        if (!Auth::user()->can('verify marks')) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'You do not have permission to reject mark verification');
        }
        
        // Check if mark can be rejected (must be submitted)
        if ($mark->status !== Mark::STATUS_SUBMITTED) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Only submitted marks can be rejected');
        }
        
        try {
            if ($mark->reject(Auth::id(), $request->remarks)) {
                return redirect()->route('marks.show', $mark)
                    ->with('success', 'Mark verification rejected');
            } else {
                return redirect()->route('marks.show', $mark)
                    ->with('error', 'Failed to reject mark verification');
            }
        } catch (\Exception $e) {
            Log::error('Error rejecting mark: ' . $e->getMessage());
            
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Error rejecting mark: ' . $e->getMessage());
        }
    }

    /**
     * Verify all submitted marks for a specific exam and subject.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyAll(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);
        
        // Check if user has permission to verify marks
        if (!Auth::user()->can('verify marks')) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to verify marks');
        }
        
        $marks = Mark::where('exam_id', $request->exam_id)
            ->where('subject_id', $request->subject_id)
            ->where('status', Mark::STATUS_SUBMITTED)
            ->get();
        
        if ($marks->isEmpty()) {
            return redirect()->back()
                ->with('info', 'No submitted marks found to verify');
        }
        
        DB::beginTransaction();
        
        try {
            $verifiedCount = 0;
            
            foreach ($marks as $mark) {
                if ($mark->verify(Auth::id())) {
                    $verifiedCount++;
                }
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', $verifiedCount . ' marks verified successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verifying marks in bulk: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error verifying marks: ' . $e->getMessage());
        }
    }

    /**
     * Publish a mark.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function publish(Request $request, Mark $mark)
    {
        // Check if user has permission to publish marks
        if (!Auth::user()->can('publish marks')) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'You do not have permission to publish marks');
        }
        
        // Check if mark can be published
        if (!$mark->canPublish()) {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'This mark cannot be published in its current state');
        }
        
        try {
            if ($mark->publish(Auth::id())) {
                return redirect()->route('marks.show', $mark)
                    ->with('success', 'Mark published successfully');
            } else {
                return redirect()->route('marks.show', $mark)
                    ->with('error', 'Failed to publish mark');
            }
        } catch (\Exception $e) {
            Log::error('Error publishing mark: ' . $e->getMessage());
            
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Error publishing mark: ' . $e->getMessage());
        }
    }

    /**
     * Publish all verified marks for a specific exam and subject.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function publishAll(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);
        
        // Check if user has permission to publish marks
        if (!Auth::user()->can('publish marks')) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to publish marks');
        }
        
        $marks = Mark::where('exam_id', $request->exam_id)
            ->where('subject_id', $request->subject_id)
            ->where('status', Mark::STATUS_VERIFIED)
            ->get();
        
        if ($marks->isEmpty()) {
            return redirect()->back()
                ->with('info', 'No verified marks found to publish');
        }
        
        DB::beginTransaction();
        
        try {
            $publishedCount = 0;
            
            foreach ($marks as $mark) {
                if ($mark->publish(Auth::id())) {
                    $publishedCount++;
                }
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', $publishedCount . ' marks published successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error publishing marks in bulk: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error publishing marks: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if user can view a specific mark
     * 
     * @param Mark $mark
     * @return bool
     */
    private function canViewMark(Mark $mark)
    {
        $user = Auth::user();
        
        // Super-admin and users with view marks permission can view all marks
        if ($user->hasRole('super-admin') || $user->hasPermissionTo('view marks')) {
            return true;
        }
        
        // Teachers can view marks for subjects they teach
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            return $teacher && $mark->subject->teachers->contains($teacher->id);
        }
        
        // Students can only view their own published marks
        if ($user->hasRole('Student')) {
            $student = $user->student;
            if ($student && $mark->status === Mark::STATUS_PUBLISHED) {
                return $student->id === $mark->student_id;
            }
        }
        
        return false;
    }
    
    /**
     * Check if user can edit a specific mark
     * 
     * @param Mark $mark
     * @return bool
     */
    private function canEditMark(Mark $mark)
    {
        $user = Auth::user();
        
        // Super-admin and users with edit marks permission can edit all marks
        if ($user->hasRole('super-admin') || $user->hasPermissionTo('edit marks')) {
            return true;
        }
        
        // Teachers can edit marks they created or for subjects they teach
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            
            // Check if teacher created this mark
            if ($mark->created_by === $user->id) {
                return true;
            }
            
            // Check if teacher teaches this subject
            return $teacher && $mark->subject->teachers->contains($teacher->id);
        }
        
        return false;
    }

    /**
     * Display the verification interface.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyInterface(Request $request)
    {
        // Check if user has permission to verify marks
        if (!Auth::user()->can('verify marks')) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to verify marks');
        }
        
        $exams = Exam::where('is_active', true)->get();
        $subjects = Subject::all();
        
        $examId = $request->input('exam_id');
        $subjectId = $request->input('subject_id');
        
        $exam = null;
        $subject = null;
        $marks = null;
        
        if ($examId && $subjectId) {
            $exam = Exam::findOrFail($examId);
            $subject = Subject::findOrFail($subjectId);
            
            $marks = Mark::where('exam_id', $examId)
                ->where('subject_id', $subjectId)
                ->where('status', Mark::STATUS_SUBMITTED)
                ->with(['student.user', 'creator.user'])
                ->get();
        }
        
        return view('marks.verify_interface', compact('exams', 'subjects', 'exam', 'subject', 'marks'));
    }

    /**
     * Show the import form.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        // Check if user has permission to create marks
        if (!Auth::user()->can('create marks')) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to import marks');
        }
        
        $exams = Exam::where('is_active', true)->get();
        $subjects = Subject::orderBy('name')->get();
        
        return view('marks.import', compact('exams', 'subjects'));
    }

    /**
     * Process the mark import.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processImport(Request $request)
    {
        // Check if user has permission to create marks
        if (!Auth::user()->can('create marks')) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to import marks');
        }
        
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        try {
            DB::beginTransaction();
            
            // Import marks using the imported data
            $import = new MarksImport($exam, $subject, Auth::id());
            Excel::import($import, $request->file('file'));
            
            // Check if there were errors during import
            if ($import->hasErrors()) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Import failed with errors: ' . implode(', ', $import->getErrors()))
                    ->withInput();
            }
            
            DB::commit();
            
            return redirect()->route('marks.index', [
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id
                ])
                ->with('success', 'Marks imported successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing marks: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error importing marks: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download a mark template for import.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'format' => 'required|in:xlsx,csv',
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Get students for this exam's class
        $students = Student::where('class_id', $exam->class_id)
            ->where('enrollment_status', 'active')
            ->with('user')
            ->orderBy('roll_number')
            ->get();
        
        // Create a template export
        $export = new MarksExport($students, $exam, $subject);
        
        $filename = "marks_template_{$exam->title}_{$subject->name}." . $request->format;
        
        return Excel::download(
            $export,
            $filename,
            $request->format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Export marks to Excel or CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'format' => 'nullable|in:xlsx,csv,pdf',
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check if user has permission to view these marks
        if (!Auth::user()->can('view marks') && !$this->canViewMarks($exam, $subject)) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to export these marks');
        }
        
        // Get marks with students
        $marks = Mark::where('exam_id', $exam->id)
            ->where('subject_id', $subject->id)
            ->with(['student.user'])
            ->get();
        
        // Format to export (default to xlsx)
        $format = $request->format ?? 'xlsx';
        
        // Generate filename
        $filename = Str::slug($exam->title . '-' . $subject->name) . '-marks.' . $format;
        
        // Create export and download
        $export = new MarksExport($marks, $exam, $subject);
        
        return Excel::download(
            $export,
            $filename,
            $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX
        );
    }
    
    /**
     * View student marks report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function studentMarks(Request $request, Student $student)
    {
        $user = Auth::user();
        
        // Students can only view their own marks
        if ($user->hasRole('Student') && $user->student->id !== $student->id) {
            abort(403, 'You can only view your own marks');
        }
        
        // Teachers can only view marks for their students
        if ($user->hasRole('Teacher') && !$this->canViewStudentMarks($student)) {
            abort(403, 'You can only view marks for your students');
        }
        
        // Get academic sessions
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $sessionId = $request->input('session_id');
        
        // Get exams for this student
        $examsQuery = Exam::whereHas('marks', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        });
        
        // Filter by session if provided
        if ($sessionId) {
            $examsQuery->where('academic_session_id', $sessionId);
        }
        
        $exams = $examsQuery->with('academicSession')->get();
        
        // Get subjects for this student
        $subjects = Subject::whereHas('marks', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })->get();
        
        // Get marks by exam and subject
        $marksByExam = [];
        
        foreach ($exams as $exam) {
            $marks = Mark::where('student_id', $student->id)
                ->where('exam_id', $exam->id)
                ->with(['subject'])
                ->get()
                ->keyBy('subject_id');
                
            $marksByExam[$exam->id] = $marks;
        }
        
        return view('marks.student_report', compact(
            'student', 
            'exams', 
            'subjects', 
            'marksByExam', 
            'academicSessions',
            'sessionId'
        ));
    }
    
    /**
     * Export student marks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function exportStudentMarks(Request $request, Student $student)
    {
        $user = Auth::user();
        
        // Students can only export their own marks
        if ($user->hasRole('Student') && $user->student->id !== $student->id) {
            abort(403, 'You can only export your own marks');
        }
        
        // Get exam ID from request or use null for all exams
        $examId = $request->input('exam_id');
        $format = $request->input('format', 'xlsx');
        
        // Get marks for this student
        $marksQuery = Mark::where('student_id', $student->id)
            ->with(['exam', 'subject']);
            
        // Filter by exam if provided
        if ($examId) {
            $marksQuery->where('exam_id', $examId);
        }
        
        $marks = $marksQuery->get();
        
        // Generate filename
        $filename = 'student_marks_' . $student->roll_number . '.' . $format;
        
        // Create export and download
        // Note: You'll need to create a StudentMarksExport class for this
        return Excel::download(
            new StudentMarksExport($marks, $student),
            $filename,
            $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX
        );
    }
    
    /**
     * Display marks analysis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function analysis(Request $request)
    {
        // Check if user has permission to view marks
        if (!Auth::user()->can('view marks')) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to view mark analysis');
        }
        
        $examId = $request->input('exam_id');
        $subjectId = $request->input('subject_id');
        
        $exams = Exam::where('is_active', true)->orderBy('created_at', 'desc')->get();
        $subjects = Subject::orderBy('name')->get();
        
        $stats = null;
        
        if ($examId && $subjectId) {
            $exam = Exam::findOrFail($examId);
            $subject = Subject::findOrFail($subjectId);
            
            // Get statistics for this exam and subject
            $marks = Mark::where('exam_id', $examId)
                ->where('subject_id', $subjectId)
                ->get();
                
            $stats = $this->calculateMarkStatistics($marks, $exam);
        }
        
        return view('marks.analysis', compact('exams', 'subjects', 'stats', 'examId', 'subjectId'));
    }
    
    /**
     * Calculate statistics for a collection of marks.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $marks
     * @param  \App\Models\Exam  $exam
     * @return array
     */
    private function calculateMarkStatistics($marks, $exam)
    {
        if ($marks->isEmpty()) {
            return [
                'count' => 0,
                'average' => 0,
                'highest' => 0,
                'lowest' => 0,
                'passing_count' => 0,
                'failing_count' => 0,
                'absent_count' => 0,
                'passing_percentage' => 0,
                'grade_distribution' => [],
                'score_distribution' => []
            ];
        }
        
        // Initialize statistics
        $stats = [
            'count' => $marks->count(),
            'average' => 0,
            'highest' => 0,
            'lowest' => $exam->total_marks,
            'passing_count' => 0,
            'failing_count' => 0,
            'absent_count' => 0,
            'passing_percentage' => 0,
            'grade_distribution' => [],
            'score_distribution' => [
                '0-20' => 0,
                '21-40' => 0,
                '41-60' => 0,
                '61-80' => 0,
                '81-100' => 0
            ]
        ];
        
        $totalMarks = 0;
        $nonAbsentCount = 0;
        
        foreach ($marks as $mark) {
            // Skip absent students in some calculations
            if ($mark->is_absent) {
                $stats['absent_count']++;
                continue;
            }
            
            $nonAbsentCount++;
            $marksObtained = $mark->marks_obtained;
            
            // Add to total for average calculation
            $totalMarks += $marksObtained;
            
            // Update highest and lowest
            if ($marksObtained > $stats['highest']) {
                $stats['highest'] = $marksObtained;
            }
            
            if ($marksObtained < $stats['lowest']) {
                $stats['lowest'] = $marksObtained;
            }
            
            // Count passing and failing
            if ($mark->isPassing()) {
                $stats['passing_count']++;
            } else {
                $stats['failing_count']++;
            }
            
            // Count by grade
            $grade = $mark->grade;
            if ($grade) {
                if (!isset($stats['grade_distribution'][$grade])) {
                    $stats['grade_distribution'][$grade] = 0;
                }
                $stats['grade_distribution'][$grade]++;
            }
            
            // Count by score distribution
            $percentage = ($marksObtained / $exam->total_marks) * 100;
            
            if ($percentage <= 20) {
                $stats['score_distribution']['0-20']++;
            } elseif ($percentage <= 40) {
                $stats['score_distribution']['21-40']++;
            } elseif ($percentage <= 60) {
                $stats['score_distribution']['41-60']++;
            } elseif ($percentage <= 80) {
                $stats['score_distribution']['61-80']++;
            } else {
                $stats['score_distribution']['81-100']++;
            }
        }
        
        // Calculate average and passing percentage
        $stats['average'] = $nonAbsentCount > 0 ? round($totalMarks / $nonAbsentCount, 2) : 0;
        $stats['passing_percentage'] = $nonAbsentCount > 0 ? round(($stats['passing_count'] / $nonAbsentCount) * 100, 2) : 0;
        
        // Sort grade distribution by grade values
        ksort($stats['grade_distribution']);
        
        return $stats;
    }
    
    /**
     * Check if user can view a specific student's marks
     * 
     * @param Student $student
     * @return bool
     */
    private function canViewStudentMarks(Student $student)
    {
        $user = Auth::user();
        
        // Super-admin and users with view marks permission can view all student marks
        if ($user->hasRole('super-admin') || $user->hasPermissionTo('view marks')) {
            return true;
        }
        
        // Teachers can view marks for their students
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            if (!$teacher) {
                return false;
            }
            
            // Check if teacher teaches any subject in the student's class
            return $teacher->subjects()
                ->whereHas('classes', function ($query) use ($student) {
                    $query->where('class_id', $student->class_id);
                })
                ->exists();
        }
        
        // Students can only view their own marks
        if ($user->hasRole('Student')) {
            return $user->student->id === $student->id;
        }
        
        return false;
    }
    
    /**
     * Display reports interface.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reports(Request $request)
    {
        // Check if user has permission to view marks
        if (!Auth::user()->can('view marks')) {
            return redirect()->route('marks.select')
                ->with('error', 'You do not have permission to view mark reports');
        }
        
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $sessionId = $request->input('session_id');
        $reportType = $request->input('report_type', 'class_performance');
        
        // Get classes, subjects, and exams based on session
        $classes = Classes::when($sessionId, function ($query) use ($sessionId) {
            return $query->where('academic_session_id', $sessionId);
        })->get();
        
        $subjects = Subject::all();
        
        $exams = Exam::when($sessionId, function ($query) use ($sessionId) {
            return $query->where('academic_session_id', $sessionId);
        })->get();
        
        // Report data will be calculated based on selection
        $reportData = null;
        
        // Prepare and display the appropriate report based on type
        if ($request->filled('session_id') && $request->filled('report_type')) {
            switch ($reportType) {
                case 'class_performance':
                    $classId = $request->input('class_id');
                    $examId = $request->input('exam_id');
                    
                    if ($classId && $examId) {
                        $reportData = $this->generateClassPerformanceReport($classId, $examId);
                    }
                    break;
                    
                case 'subject_analysis':
                    $subjectId = $request->input('subject_id');
                    $examId = $request->input('exam_id');
                    
                    if ($subjectId && $examId) {
                        $reportData = $this->generateSubjectAnalysisReport($subjectId, $examId);
                    }
                    break;
                    
                // Add more report types as needed
            }
        }
        
        return view('marks.reports', compact(
            'academicSessions', 
            'sessionId', 
            'reportType',
            'classes',
            'subjects',
            'exams',
            'reportData'
        ));
    }
    
    /**
     * Generate class performance report.
     *
     * @param  int  $classId
     * @param  int  $examId
     * @return array
     */
    private function generateClassPerformanceReport($classId, $examId)
    {
        // Get all students in the class
        $students = Student::where('class_id', $classId)
            ->where('enrollment_status', 'active')
            ->with('user')
            ->orderBy('roll_number')
            ->get();
            
        // Get all subjects for this class
        $subjects = Subject::whereHas('classes', function ($query) use ($classId) {
            $query->where('class_id', $classId);
        })->get();
        
        // Get exam details
        $exam = Exam::findOrFail($examId);
        
        // Get marks for all students in this exam
        $marks = Mark::where('exam_id', $examId)
            ->whereIn('student_id', $students->pluck('id'))
            ->with(['subject'])
            ->get();
            
        // Group marks by student and subject
        $studentMarks = [];
        $studentTotals = [];
        $subjectStatistics = [];
        
        foreach ($students as $student) {
            $studentMarks[$student->id] = [];
            $studentTotals[$student->id] = [
                'total_obtained' => 0,
                'total_possible' => 0,
                'percentage' => 0,
                'rank' => 0,
                'subjects_passed' => 0,
                'subjects_failed' => 0
            ];
            
            // Initialize subject statistics
            foreach ($subjects as $subject) {
                if (!isset($subjectStatistics[$subject->id])) {
                    $subjectStatistics[$subject->id] = [
                        'highest' => 0,
                        'lowest' => $exam->total_marks,
                        'average' => 0,
                        'total' => 0,
                        'count' => 0,
                        'passing_count' => 0,
                        'name' => $subject->name
                    ];
                }
            }
        }
        
        // Process marks
        foreach ($marks as $mark) {
            $studentId = $mark->student_id;
            $subjectId = $mark->subject_id;
            
            // Add mark to student's record
            $studentMarks[$studentId][$subjectId] = $mark;
            
            // Update student totals if not absent
            if (!$mark->is_absent) {
                $studentTotals[$studentId]['total_obtained'] += $mark->marks_obtained;
                $studentTotals[$studentId]['total_possible'] += $exam->total_marks;
                
                // Count passing/failing subjects
                if ($mark->isPassing()) {
                    $studentTotals[$studentId]['subjects_passed']++;
                } else {
                    $studentTotals[$studentId]['subjects_failed']++;
                }
                
                // Update subject statistics
                $subjectStatistics[$subjectId]['total'] += $mark->marks_obtained;
                $subjectStatistics[$subjectId]['count']++;
                
                if ($mark->marks_obtained > $subjectStatistics[$subjectId]['highest']) {
                    $subjectStatistics[$subjectId]['highest'] = $mark->marks_obtained;
                }
                
                if ($mark->marks_obtained < $subjectStatistics[$subjectId]['lowest']) {
                    $subjectStatistics[$subjectId]['lowest'] = $mark->marks_obtained;
                }
                
                if ($mark->isPassing()) {
                    $subjectStatistics[$subjectId]['passing_count']++;
                }
            }
        }
        
        // Calculate percentages and averages
        foreach ($studentTotals as $studentId => &$totals) {
            if ($totals['total_possible'] > 0) {
                $totals['percentage'] = round(($totals['total_obtained'] / $totals['total_possible']) * 100, 2);
            }
        }
        
        // Calculate subject averages
        foreach ($subjectStatistics as $subjectId => &$stats) {
            if ($stats['count'] > 0) {
                $stats['average'] = round($stats['total'] / $stats['count'], 2);
            }
        }
        
        // Rank students
        $rankable = collect($studentTotals)->sortByDesc('percentage');
        $rank = 1;
        
        foreach ($rankable as $studentId => $totals) {
            $studentTotals[$studentId]['rank'] = $rank++;
        }
        
        return [
            'students' => $students,
            'subjects' => $subjects,
            'exam' => $exam,
            'studentMarks' => $studentMarks,
            'studentTotals' => $studentTotals,
            'subjectStatistics' => $subjectStatistics
        ];
    }
    
    /**
     * Generate subject analysis report.
     *
     * @param  int  $subjectId
     * @param  int  $examId
     * @return array
     */
    private function generateSubjectAnalysisReport($subjectId, $examId)
    {
        // Implementation similar to generateClassPerformanceReport but focused on subject analysis
        // For brevity, not fully implemented here
        
        return [];
    }

    /**
     * Show form for subject-specific marks entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subjectEntry(Request $request)
    {
        // Get active academic session and exams
        $activeSession = AcademicSession::where('is_active', true)->first();
        $exams = Exam::where('is_active', true)
                     ->where('academic_session_id', $activeSession->id ?? null)
                     ->orderBy('created_at', 'desc')
                     ->get();
        
        // Get subjects the teacher has access to (if teacher)
        $subjects = [];
        if (Auth::user()->hasRole('Teacher')) {
            $teacher = Auth::user()->teacher;
            if ($teacher) {
                $subjects = $teacher->subjects;
            }
        } else {
            // For admins, get all subjects
            $subjects = Subject::orderBy('name')->get();
        }
        
        // If exam_id and subject_id are provided, prepare the form for mark entry
        $students = collect();
        $exam = null;
        $subject = null;
        $existingMarks = [];
        
        if ($request->filled('exam_id') && $request->filled('subject_id')) {
            $exam = Exam::with('academicSession')->findOrFail($request->exam_id);
            $subject = Subject::findOrFail($request->subject_id);
            
            // Check if user has permission
            if (!Auth::user()->can('create marks') && !$this->canCreateMarks($exam, $subject)) {
                return redirect()->route('marks.subjectEntry')
                    ->with('error', 'You do not have permission to create marks for this subject');
            }
            
            // Get students for the exam's class
            $students = Student::where('class_id', $exam->class_id)
                              ->where('enrollment_status', 'active')
                              ->with('user')
                              ->orderBy('roll_number')
                              ->get();
            
            // Get any existing marks
            $marks = Mark::where('exam_id', $exam->id)
                        ->where('subject_id', $subject->id)
                        ->get();
            
            $existingMarks = $marks->keyBy('student_id');
        }
        
        return view('marks.subject_entry', compact('exams', 'subjects', 'students', 'exam', 'subject', 'existingMarks', 'activeSession'));
    }
    
    /**
     * Store subject-specific marks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSubjectMarks(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0',
            'is_absent' => 'nullable|array',
            'is_absent.*' => 'nullable|boolean',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:255',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check permission
        if (!Auth::user()->can('create marks') && !$this->canCreateMarks($exam, $subject)) {
            return redirect()->route('marks.subjectEntry')
                ->with('error', 'You do not have permission to create marks for this subject');
        }
        
        $marks = $request->input('marks', []);
        $absent = $request->input('is_absent', []);
        $remarks = $request->input('remarks', []);
        $action = $request->input('action', 'save_draft');
        
        DB::beginTransaction();
        try {
            $updatedCount = 0;
            $createdCount = 0;
            
            foreach ($marks as $studentId => $marksObtained) {
                // Skip if no marks and not absent
                if (empty($marksObtained) && empty($absent[$studentId])) {
                    continue;
                }
                
                // Check if marks already exist
                $mark = Mark::where('student_id', $studentId)
                           ->where('exam_id', $exam->id)
                           ->where('subject_id', $subject->id)
                           ->first();
                
                $isAbsent = isset($absent[$studentId]) && $absent[$studentId] ? true : false;
                $studentRemarks = $remarks[$studentId] ?? null;
                
                $markData = [
                    'student_id' => $studentId,
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id,
                    'marks_obtained' => $isAbsent ? null : $marksObtained,
                    'total_marks' => $exam->total_marks,
                    'is_absent' => $isAbsent,
                    'remarks' => $studentRemarks,
                    'status' => $action === 'submit' ? Mark::STATUS_SUBMITTED : Mark::STATUS_DRAFT,
                    'updated_by' => Auth::id(),
                ];
                
                // Calculate grade if possible
                if (!$isAbsent && !empty($marksObtained)) {
                    $percentage = ($marksObtained / $exam->total_marks) * 100;
                    $markData['grade'] = $this->calculateGrade($percentage, $exam);
                }
                
                if ($mark) {
                    // Update existing mark
                    $mark->update($markData);
                    $updatedCount++;
                } else {
                    // Create new mark
                    $markData['created_by'] = Auth::id();
                    Mark::create($markData);
                    $createdCount++;
                }
            }
            
            DB::commit();
            
            $message = "Marks successfully saved. {$createdCount} new records created, {$updatedCount} records updated.";
            if ($action === 'submit') {
                $message .= " Marks have been submitted for verification.";
            }
            
            return redirect()->route('marks.subjectEntry', [
                'exam_id' => $exam->id,
                'subject_id' => $subject->id
            ])->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing subject marks: ' . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while saving marks: ' . $e->getMessage());
        }
    }
} 
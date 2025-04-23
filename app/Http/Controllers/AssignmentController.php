<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Subject;
use App\Models\Classes;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\StudentAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create assignments', ['only' => ['create', 'store', 'assignStudents']]);
        $this->middleware('permission:edit assignments', ['only' => ['edit', 'update', 'updateStatus']]);
        $this->middleware('permission:delete assignments', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Assignment::with(['subject', 'class', 'academicYear']);
        
        // Apply filters if provided
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Default ordering by due date, most recent first
        $assignments = $query->orderBy('due_date', 'desc')->paginate(15);
        
        // Get data for filters
        $subjects = Subject::orderBy('name')->get();
        $classes = Classes::orderBy('name')->get();
        
        // Stats
        $totalAssignments = Assignment::count();
        $upcomingAssignments = Assignment::upcoming()->count();
        $overdueAssignments = Assignment::overdue()->count();
        
        return view('assignments.index', compact(
            'assignments', 
            'subjects', 
            'classes', 
            'totalAssignments', 
            'upcomingAssignments', 
            'overdueAssignments'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        $classes = Classes::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        
        return view('assignments.create', compact('subjects', 'classes', 'academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:today',
            'max_score' => 'required|integer|min:1',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'status' => 'required|in:draft,published,archived',
            'allow_late_submission' => 'boolean',
            'late_submission_penalty' => 'nullable|integer|min:0',
            'attachment' => 'nullable|file|max:10240',
        ]);
        
        // Handle file upload if provided
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $attachmentPath = $attachment->store('assignments/attachments', 'public');
            $validatedData['attachment_path'] = $attachmentPath;
        }
        
        // Set created_by to current user's ID
        $validatedData['created_by'] = Auth::id();
        
        // Create the assignment
        $assignment = Assignment::create($validatedData);
        
        // Optionally auto-assign to all students in the class
        if ($request->has('auto_assign_students') && $validatedData['class_id']) {
            $this->assignAllStudentsInClass($assignment, $validatedData['class_id']);
        }
        
        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        // Load relationships
        $assignment->load(['subject', 'class', 'academicYear', 'creator', 'studentAssignments.student']);
        
        // Calculate statistics
        $totalStudents = $assignment->students()->count();
        $submittedCount = $assignment->getSubmissionCount();
        $gradedCount = $assignment->getGradedCount();
        $submissionRate = $totalStudents > 0 ? ($submittedCount / $totalStudents) * 100 : 0;
        $averageScore = $assignment->getAverageScore();
        
        return view('assignments.show', compact(
            'assignment', 
            'totalStudents', 
            'submittedCount', 
            'gradedCount', 
            'submissionRate', 
            'averageScore'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        $subjects = Subject::orderBy('name')->get();
        $classes = Classes::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        
        return view('assignments.edit', compact('assignment', 'subjects', 'classes', 'academicYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        // Validate the request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'required|integer|min:1',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'status' => 'required|in:draft,published,archived',
            'allow_late_submission' => 'boolean',
            'late_submission_penalty' => 'nullable|integer|min:0',
            'attachment' => 'nullable|file|max:10240',
        ]);
        
        // Set default values for checkboxes
        $validatedData['allow_late_submission'] = $request->has('allow_late_submission');
        
        // Handle file upload if provided
        if ($request->hasFile('attachment')) {
            // Delete the old file if it exists
            if ($assignment->attachment_path) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }
            
            $attachment = $request->file('attachment');
            $attachmentPath = $attachment->store('assignments/attachments', 'public');
            $validatedData['attachment_path'] = $attachmentPath;
        }
        
        // Update the assignment
        $assignment->update($validatedData);
        
        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        // Delete the attachment if it exists
        if ($assignment->attachment_path) {
            Storage::disk('public')->delete($assignment->attachment_path);
        }
        
        // Delete the assignment
        $assignment->delete();
        
        return redirect()->route('assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }
    
    /**
     * Display the form to assign students to the assignment.
     */
    public function assignStudentsForm(Assignment $assignment)
    {
        // Load already assigned students
        $assignedStudentIds = $assignment->students()->pluck('student_id')->toArray();
        
        // Get available students for the class if class_id exists
        if ($assignment->class_id) {
            $availableStudents = Student::where('class_id', $assignment->class_id)
                ->orderBy('first_name')
                ->get();
        } else {
            // Otherwise, get all active students
            $availableStudents = Student::where('status', true)
                ->orderBy('first_name')
                ->get();
        }
        
        return view('assignments.assign-students', compact('assignment', 'availableStudents', 'assignedStudentIds'));
    }
    
    /**
     * Assign students to the assignment.
     */
    public function assignStudents(Request $request, Assignment $assignment)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);
        
        // Get the student IDs
        $studentIds = $request->input('student_ids');
        
        // Sync the students to the assignment
        $assignment->students()->sync($studentIds);
        
        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Students assigned successfully.');
    }
    
    /**
     * Automatically assign all students in a class to an assignment.
     */
    private function assignAllStudentsInClass(Assignment $assignment, $classId)
    {
        $studentIds = Student::where('class_id', $classId)->pluck('id')->toArray();
        
        $assignment->students()->sync($studentIds);
    }
    
    /**
     * Download the assignment attachment.
     */
    public function downloadAttachment(Assignment $assignment)
    {
        if (!$assignment->attachment_path) {
            return redirect()->back()->with('error', 'No attachment found for this assignment.');
        }
        
        return Storage::disk('public')->download($assignment->attachment_path, $assignment->title . '_attachment.pdf');
    }
    
    /**
     * Update the status of an assignment (publish, archive, etc.).
     */
    public function updateStatus(Request $request, Assignment $assignment)
    {
        $request->validate([
            'status' => 'required|in:draft,published,archived',
        ]);
        
        $assignment->update(['status' => $request->status]);
        
        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Assignment status updated successfully.');
    }
    
    /**
     * View assignments by subject.
     */
    public function bySubject(Subject $subject)
    {
        $assignments = Assignment::where('subject_id', $subject->id)
            ->with(['class', 'academicYear'])
            ->orderBy('due_date', 'desc')
            ->paginate(15);
        
        return view('assignments.by-subject', compact('assignments', 'subject'));
    }
    
    /**
     * View assignments by class.
     */
    public function byClass(Classes $class)
    {
        $assignments = Assignment::where('class_id', $class->id)
            ->with(['subject', 'academicYear'])
            ->orderBy('due_date', 'desc')
            ->paginate(15);
        
        return view('assignments.by-class', compact('assignments', 'class'));
    }
}

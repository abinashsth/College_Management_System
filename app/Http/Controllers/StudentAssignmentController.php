<?php

namespace App\Http\Controllers;

use App\Models\StudentAssignment;
use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentAssignmentController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:grade assignments', ['only' => ['grade', 'bulkGrade']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if user is a student
        $isStudent = Auth::user()->hasRole('Student');
        
        // Get student if user is a student
        $student = null;
        if ($isStudent) {
            $student = Student::where('user_id', Auth::id())->first();
            if (!$student) {
                return redirect()->route('dashboard')->with('error', 'Student profile not found.');
            }
            
            $query = StudentAssignment::with(['assignment', 'assignment.subject'])
                ->where('student_id', $student->id);
        } else {
            // For teachers and admins
            $query = StudentAssignment::with(['assignment', 'student', 'assignment.subject']);
            
            // Apply filters if provided
            if ($request->has('assignment_id')) {
                $query->where('assignment_id', $request->assignment_id);
            }
            
            if ($request->has('student_id')) {
                $query->where('student_id', $request->student_id);
            }
            
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
        }
        
        // Default ordering
        $studentAssignments = $query->orderBy('updated_at', 'desc')->paginate(20);
        
        if ($isStudent) {
            return view('student-assignments.student-index', compact('studentAssignments', 'student'));
        } else {
            // Get data for filters
            $assignments = Assignment::orderBy('title')->get();
            $students = Student::orderBy('first_name')->get();
            
            return view('student-assignments.index', compact('studentAssignments', 'assignments', 'students'));
        }
    }

    /**
     * Show the form for submitting an assignment.
     */
    public function create()
    {
        // This method is not used as submissions are created through show/submit methods
        return redirect()->route('student-assignments.index');
    }

    /**
     * Submit an assignment (Store a newly created resource in storage).
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'student_id' => 'required|exists:students,id',
            'submission_text' => 'nullable|string',
            'submission_file' => 'nullable|file|max:10240',
        ]);
        
        // Check if the student already has a submission for this assignment
        $existingSubmission = StudentAssignment::where('assignment_id', $validatedData['assignment_id'])
            ->where('student_id', $validatedData['student_id'])
            ->first();
        
        if ($existingSubmission) {
            return redirect()->route('student-assignments.edit', $existingSubmission)
                ->with('info', 'You already have a submission for this assignment. You can edit it here.');
        }
        
        // Get the assignment
        $assignment = Assignment::findOrFail($validatedData['assignment_id']);
        
        // Handle file upload if provided
        $filePath = null;
        if ($request->hasFile('submission_file')) {
            $file = $request->file('submission_file');
            $filePath = $file->store('assignments/submissions', 'public');
        }
        
        // Create the submission
        $submission = StudentAssignment::create([
            'assignment_id' => $validatedData['assignment_id'],
            'student_id' => $validatedData['student_id'],
            'submission_text' => $validatedData['submission_text'] ?? null,
            'submission_file_path' => $filePath,
            'submitted_at' => now(),
            'is_late' => now()->gt($assignment->due_date),
            'status' => now()->gt($assignment->due_date) ? 'late' : 'submitted',
        ]);
        
        return redirect()->route('student-assignments.show', $submission)
            ->with('success', 'Assignment submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentAssignment $studentAssignment)
    {
        // Load relationships
        $studentAssignment->load(['assignment', 'student', 'grader']);
        
        // Check if the user is the student who owns this submission or has permission to view it
        $isOwner = Auth::user()->hasRole('Student') && 
                  Auth::id() == $studentAssignment->student->user_id;
        
        if (!$isOwner && !Auth::user()->can('view assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view this submission.');
        }
        
        return view('student-assignments.show', compact('studentAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentAssignment $studentAssignment)
    {
        // Load relationships
        $studentAssignment->load(['assignment', 'student']);
        
        // Check if the user is the student who owns this submission
        $isOwner = Auth::user()->hasRole('Student') && 
                  Auth::id() == $studentAssignment->student->user_id;
        
        if (!$isOwner) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to edit this submission.');
        }
        
        // Check if submission can be edited (not graded yet)
        if ($studentAssignment->score !== null) {
            return redirect()->route('student-assignments.show', $studentAssignment)
                ->with('error', 'You cannot edit a submission that has already been graded.');
        }
        
        return view('student-assignments.edit', compact('studentAssignment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentAssignment $studentAssignment)
    {
        // Validate the request
        $validatedData = $request->validate([
            'submission_text' => 'nullable|string',
            'submission_file' => 'nullable|file|max:10240',
        ]);
        
        // Check if the user is the student who owns this submission
        $isOwner = Auth::user()->hasRole('Student') && 
                  Auth::id() == $studentAssignment->student->user_id;
        
        if (!$isOwner) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to update this submission.');
        }
        
        // Check if submission can be updated (not graded yet)
        if ($studentAssignment->score !== null) {
            return redirect()->route('student-assignments.show', $studentAssignment)
                ->with('error', 'You cannot update a submission that has already been graded.');
        }
        
        // Handle file upload if provided
        if ($request->hasFile('submission_file')) {
            // Delete the old file if it exists
            if ($studentAssignment->submission_file_path) {
                Storage::disk('public')->delete($studentAssignment->submission_file_path);
            }
            
            $file = $request->file('submission_file');
            $filePath = $file->store('assignments/submissions', 'public');
            $studentAssignment->submission_file_path = $filePath;
        }
        
        // Update the submission
        $studentAssignment->submission_text = $validatedData['submission_text'] ?? $studentAssignment->submission_text;
        $studentAssignment->submitted_at = now();
        $studentAssignment->is_late = now()->gt($studentAssignment->assignment->due_date);
        $studentAssignment->status = $studentAssignment->is_late ? 'late' : 'submitted';
        $studentAssignment->save();
        
        return redirect()->route('student-assignments.show', $studentAssignment)
            ->with('success', 'Submission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAssignment $studentAssignment)
    {
        // Check if the user has permission to delete submissions
        if (!Auth::user()->can('delete assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to delete submissions.');
        }
        
        // Delete the submission file if it exists
        if ($studentAssignment->submission_file_path) {
            Storage::disk('public')->delete($studentAssignment->submission_file_path);
        }
        
        // Delete the submission
        $studentAssignment->delete();
        
        return redirect()->route('student-assignments.index')
            ->with('success', 'Submission deleted successfully.');
    }
    
    /**
     * Show the form for grading a submission.
     */
    public function gradeForm(StudentAssignment $studentAssignment)
    {
        // Check if the user has permission to grade assignments
        if (!Auth::user()->can('grade assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to grade assignments.');
        }
        
        // Load relationships
        $studentAssignment->load(['assignment', 'student']);
        
        return view('student-assignments.grade', compact('studentAssignment'));
    }
    
    /**
     * Grade a submission.
     */
    public function grade(Request $request, StudentAssignment $studentAssignment)
    {
        // Check if the user has permission to grade assignments
        if (!Auth::user()->can('grade assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to grade assignments.');
        }
        
        // Validate the request
        $validatedData = $request->validate([
            'score' => 'required|integer|min:0|max:' . $studentAssignment->assignment->max_score,
            'feedback' => 'nullable|string',
        ]);
        
        // Grade the submission
        $studentAssignment->grade(
            $validatedData['score'],
            $validatedData['feedback'] ?? null,
            Auth::id()
        );
        
        return redirect()->route('student-assignments.show', $studentAssignment)
            ->with('success', 'Submission graded successfully.');
    }
    
    /**
     * Download the submission file.
     */
    public function downloadSubmission(StudentAssignment $studentAssignment)
    {
        // Check if the user has permission to view the submission
        $isOwner = Auth::user()->hasRole('Student') && 
                  Auth::id() == $studentAssignment->student->user_id;
        
        if (!$isOwner && !Auth::user()->can('view assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to download this submission.');
        }
        
        if (!$studentAssignment->submission_file_path) {
            return redirect()->back()->with('error', 'No file was submitted for this assignment.');
        }
        
        return Storage::disk('public')->download(
            $studentAssignment->submission_file_path, 
            $studentAssignment->student->name . '_' . $studentAssignment->assignment->title . '_submission.pdf'
        );
    }
    
    /**
     * Show assignments for a specific student.
     */
    public function studentAssignments(Student $student)
    {
        // Check if the user has permission to view the student's assignments
        $isOwner = Auth::user()->hasRole('Student') && 
                  Auth::id() == $student->user_id;
        
        if (!$isOwner && !Auth::user()->can('view assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view this student\'s assignments.');
        }
        
        $studentAssignments = StudentAssignment::with(['assignment', 'assignment.subject'])
            ->where('student_id', $student->id)
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);
        
        return view('student-assignments.student-assignments', compact('studentAssignments', 'student'));
    }
    
    /**
     * Show submissions for a specific assignment.
     */
    public function assignmentSubmissions(Assignment $assignment)
    {
        // Check if the user has permission to view assignment submissions
        if (!Auth::user()->can('view assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view assignment submissions.');
        }
        
        $studentAssignments = StudentAssignment::with(['student'])
            ->where('assignment_id', $assignment->id)
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);
        
        // Calculate statistics
        $totalStudents = $assignment->students()->count();
        $submittedCount = $assignment->getSubmissionCount();
        $gradedCount = $assignment->getGradedCount();
        $submissionRate = $totalStudents > 0 ? ($submittedCount / $totalStudents) * 100 : 0;
        $averageScore = $assignment->getAverageScore();
        
        return view('student-assignments.assignment-submissions', compact(
            'studentAssignments', 
            'assignment', 
            'totalStudents', 
            'submittedCount',
            'gradedCount',
            'submissionRate',
            'averageScore'
        ));
    }
    
    /**
     * Return a submission to the student for revision.
     */
    public function returnForRevision(Request $request, StudentAssignment $studentAssignment)
    {
        // Check if the user has permission to grade assignments
        if (!Auth::user()->can('grade assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to return assignments for revision.');
        }
        
        // Validate the request
        $validatedData = $request->validate([
            'feedback' => 'required|string',
        ]);
        
        // Return the submission for revision
        $studentAssignment->returnForRevision($validatedData['feedback']);
        
        return redirect()->route('student-assignments.show', $studentAssignment)
            ->with('success', 'Assignment returned for revision successfully.');
    }
    
    /**
     * Show form for bulk grading submissions for an assignment.
     */
    public function bulkGradeForm(Assignment $assignment)
    {
        // Check if the user has permission to grade assignments
        if (!Auth::user()->can('grade assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to grade assignments.');
        }
        
        $studentAssignments = StudentAssignment::with(['student'])
            ->where('assignment_id', $assignment->id)
            ->whereNotNull('submitted_at')
            ->orderBy('student_id')
            ->get();
        
        return view('student-assignments.bulk-grade', compact('studentAssignments', 'assignment'));
    }
    
    /**
     * Process bulk grading of submissions.
     */
    public function bulkGrade(Request $request, Assignment $assignment)
    {
        // Check if the user has permission to grade assignments
        if (!Auth::user()->can('grade assignments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to grade assignments.');
        }
        
        // Validate the request
        $validatedData = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'nullable|integer|min:0|max:' . $assignment->max_score,
            'feedback' => 'required|array',
            'feedback.*' => 'nullable|string',
        ]);
        
        // Process each submission
        foreach ($validatedData['scores'] as $submissionId => $score) {
            if ($score !== null) {
                $studentAssignment = StudentAssignment::find($submissionId);
                if ($studentAssignment && $studentAssignment->assignment_id == $assignment->id) {
                    $studentAssignment->grade(
                        $score,
                        $validatedData['feedback'][$submissionId] ?? null,
                        Auth::id()
                    );
                }
            }
        }
        
        return redirect()->route('student-assignments.assignment-submissions', $assignment)
            ->with('success', 'Submissions graded successfully.');
    }
}

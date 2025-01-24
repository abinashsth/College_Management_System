<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\AcademicSession;
use App\Models\ExaminerAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExaminerAssignmentController extends Controller
{
    public function index()
    {
        $assignments = ExaminerAssignment::with(['user', 'class', 'subject', 'academicSession'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('exam-management.examiner-assignments.index', compact('assignments'));
    }

    public function create()
    {
        $examiners = User::role('examiner')->get();
        $classes = Classes::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $academicSessions = AcademicSession::where('is_active', true)->get();
        
        return view('exam-management.examiner-assignments.create', compact('examiners', 'classes', 'subjects', 'academicSessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
        ]);

        // Check for duplicate assignment
        $exists = ExaminerAssignment::where($validated)->exists();
        if ($exists) {
            return back()->with('error', 'This assignment already exists.');
        }

        ExaminerAssignment::create($validated);
        return redirect()->route('examiner-assignments.index')->with('success', 'Examiner assigned successfully.');
    }

    public function edit(ExaminerAssignment $examinerAssignment)
    {
        $examiners = User::role('examiner')->get();
        $classes = Classes::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $academicSessions = AcademicSession::where('is_active', true)->get();
        
        return view('exam-management.examiner-assignments.edit', compact('examinerAssignment', 'examiners', 'classes', 'subjects', 'academicSessions'));
    }

    public function update(Request $request, ExaminerAssignment $examinerAssignment)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'is_active' => 'boolean'
        ]);

        // Check for duplicate assignment
        $exists = ExaminerAssignment::where($validated)
            ->where('id', '!=', $examinerAssignment->id)
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'This assignment already exists.');
        }

        $examinerAssignment->update($validated);
        return redirect()->route('examiner-assignments.index')->with('success', 'Assignment updated successfully.');
    }

    public function destroy(ExaminerAssignment $examinerAssignment)
    {
        $examinerAssignment->delete();
        return redirect()->route('examiner-assignments.index')->with('success', 'Assignment removed successfully.');
    }
}

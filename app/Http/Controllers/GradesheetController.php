<?php

namespace App\Http\Controllers;

use App\Models\Gradesheet;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Session;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GradesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gradesheets = Gradesheet::with(['student', 'class', 'session'])->orderBy('id', 'desc')->paginate(10);
        return view('gradesheets.index', compact('gradesheets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::all();
        $classes = Classes::all();
        $sessions = Session::where('status', 'active')->get();
        
        return view('gradesheets.create', compact('students', 'classes', 'sessions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'session_id' => 'required|exists:sessions,id',
            'total_marks' => 'required|integer|min:0',
            'obtained_marks' => 'required|integer|min:0|lte:total_marks',
            'percentage' => 'required|numeric|min:0|max:100',
            'grade' => 'required|string|max:10',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Gradesheet::create([
            'student_id' => $request->student_id,
            'class_id' => $request->class_id,
            'session_id' => $request->session_id,
            'total_marks' => $request->total_marks,
            'obtained_marks' => $request->obtained_marks,
            'percentage' => $request->percentage,
            'grade' => $request->grade,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('gradesheets.index')
            ->with('success', 'Gradesheet created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gradesheet $gradesheet)
    {
        $gradesheet->load(['student', 'class', 'session']);
        return view('gradesheets.show', compact('gradesheet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gradesheet $gradesheet)
    {
        $students = Student::all();
        $classes = Classes::all();
        $sessions = Session::all();
        
        return view('gradesheets.edit', compact('gradesheet', 'students', 'classes', 'sessions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gradesheet $gradesheet)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'session_id' => 'required|exists:sessions,id',
            'total_marks' => 'required|integer|min:0',
            'obtained_marks' => 'required|integer|min:0|lte:total_marks',
            'percentage' => 'required|numeric|min:0|max:100',
            'grade' => 'required|string|max:10',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gradesheet->update([
            'student_id' => $request->student_id,
            'class_id' => $request->class_id,
            'session_id' => $request->session_id,
            'total_marks' => $request->total_marks,
            'obtained_marks' => $request->obtained_marks,
            'percentage' => $request->percentage,
            'grade' => $request->grade,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('gradesheets.index')
            ->with('success', 'Gradesheet updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gradesheet $gradesheet)
    {
        $gradesheet->delete();

        return redirect()->route('gradesheets.index')
            ->with('success', 'Gradesheet deleted successfully.');
    }

    /**
     * Generate gradesheet for a student based on exam results.
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'session_id' => 'required|exists:sessions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student = Student::findOrFail($request->student_id);
        $session = Session::findOrFail($request->session_id);
        $school = \App\Models\School::getActive();

        if (!$school) {
            return redirect()->back()
                ->withErrors(['error' => 'No active school found. Please set up school details first.'])
                ->withInput();
        }

        // Get all exam results for the student in the given session
        $examResults = ExamResult::join('exams', 'exam_results.exam_id', '=', 'exams.id')
            ->join('subjects', 'exams.subject_id', '=', 'subjects.id')
            ->where('exam_results.student_id', $student->id)
            ->where('subjects.class_id', $student->class_id)
            ->where('exam_results.status', 'approved')
            ->select(
                'exam_results.marks_obtained',
                'exams.max_marks',
                'subjects.subject_name',
                'exams.exam_name'
            )
            ->get();

        if ($examResults->isEmpty()) {
            return redirect()->back()
                ->withErrors(['error' => 'No approved exam results found for this student in the current session.'])
                ->withInput();
        }

        $totalMarks = $examResults->sum('max_marks');
        $obtainedMarks = $examResults->sum('marks_obtained');
        $percentage = ($obtainedMarks / $totalMarks) * 100;

        // Determine grade based on percentage
        $grade = $this->calculateGrade($percentage);

        // Create or update gradesheet
        $gradesheet = Gradesheet::updateOrCreate(
            [
                'student_id' => $student->id,
                'class_id' => $student->class_id,
                'session_id' => $session->id,
            ],
            [
                'total_marks' => $totalMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => $percentage,
                'grade' => $grade,
                'remarks' => 'Generated automatically based on exam results.',
            ]
        );

        return view('gradesheets.view', compact('gradesheet', 'student', 'session', 'school', 'examResults'));
    }

    /**
     * Calculate grade based on percentage.
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) {
            return 'A+';
        } elseif ($percentage >= 80) {
            return 'A';
        } elseif ($percentage >= 70) {
            return 'B+';
        } elseif ($percentage >= 60) {
            return 'B';
        } elseif ($percentage >= 50) {
            return 'C+';
        } elseif ($percentage >= 40) {
            return 'C';
        } elseif ($percentage >= 33) {
            return 'D';
        } else {
            return 'F';
        }
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Session;
use App\Models\Faculty;
use App\Models\Classes;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::query();

        // Apply filters
        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $exams = $query->latest()->paginate(10);
        $sessions = Session::all();
        $faculties = Faculty::all();
        $classes = Classes::all();

        return view('exams.index', compact('exams', 'sessions', 'faculties', 'classes'));
    }

    public function create()
    {
        $sessions = Session::all();
        $faculties = Faculty::all();
        $classes = Classes::all();
        return view('exams.create', compact('sessions', 'faculties', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'session_id' => 'required|exists:sessions,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'class_id' => 'required|exists:classes,id',
            'exam_date' => 'required|date',
            'status' => 'required|in:active,inactive'
        ]);

        Exam::create($validated);

        return redirect()->route('exams.index')
            ->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam)
    {
        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $sessions = Session::all();
        $faculties = Faculty::all();
        $classes = Classes::all();
        return view('exams.edit', compact('exam', 'sessions', 'faculties', 'classes'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'session_id' => 'required|exists:sessions,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'class_id' => 'required|exists:classes,id',
            'exam_date' => 'required|date',
            'status' => 'required|in:active,inactive'
        ]);

        $exam->update($validated);

        return redirect()->route('exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    public function view(Request $request)
    {
        $query = Exam::query();

        if ($request->filled('academic_year')) {
            $query->where('session_id', $request->academic_year);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('exam_term')) {
            $query->where('exam_term', $request->exam_term);
        }

        $exams = $query->with(['session', 'class'])->get();
        $academicYears = Session::all();
        $classes = Classes::all();

        return view('exams.view', compact('exams', 'academicYears', 'classes'));
    }

    // public function enterMarks(Exam $exam)
    // {
    //     // Add logic for entering marks
    //     return view('exams.index', compact('exam'));
    // }

    // public function viewResults(Exam $exam)
    // {
    //     // Add logic for viewing results
    //     return view('exams.index', compact('exam'));
    // }
} 
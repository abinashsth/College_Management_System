<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Classes;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage exams']);
    }

    public function index()
    {
        $exams = Exam::with('class')->paginate(10);
        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        $classes = Classes::all();
        return view('exams.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
            'status' => 'boolean'
        ]);

        Exam::create($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Exam created successfully');
    }

    public function edit(Exam $exam)
    {
        $classes = Classes::all();
        return view('exams.edit', compact('exam', 'classes'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
            'status' => 'boolean'
        ]);

        $exam->update($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Exam updated successfully');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('exams.index')
            ->with('success', 'Exam deleted successfully');
    }

    public function grade(Exam $exam)
    {
        $students = $exam->class->students;
        return view('exams.grade', compact('exam', 'students'));
    }

    public function updateGrades(Request $request, Exam $exam)
    {
        $request->validate([
            'grades.*' => 'nullable|numeric|min:0|max:'.$exam->total_marks,
            'remarks.*' => 'nullable|string'
        ]);

        foreach ($request->grades as $studentId => $grade) {
            $exam->students()->syncWithoutDetaching([
                $studentId => [
                    'grade' => $grade,
                    'remarks' => $request->remarks[$studentId] ?? null
                ]
            ]);
        }

        return redirect()->route('exams.index')
            ->with('success', 'Grades updated successfully');
    }
}

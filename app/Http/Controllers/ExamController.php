<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\ExamType;
use App\Models\AcademicSession;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        return view('academic.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('academic.exams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'status' => 'required|boolean'
        ]);

        Exam::create($request->all());

        return redirect()->route('academic.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam)
    {
        return view('academic.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        return view('academic.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required', 
            'total_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'status' => 'required|boolean'
        ]);

        $exam->update($request->all());

        return redirect()->route('academic.exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('academic.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }
}

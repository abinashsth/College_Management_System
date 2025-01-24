<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classes;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Mark;
use App\Models\Subject;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view results', ['only' => ['studentsIndex', 'classesIndex', 'examsIndex', 'analysisIndex', 'show']]);
        $this->middleware('permission:create results', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit results', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete results', ['only' => ['destroy']]);
    }

    public function studentsIndex()
    {
        $students = Student::with(['class', 'exams'])->get();
        return view('results.students', compact('students'));
    }

    public function classesIndex()
    {
        $classes = Classes::with(['students', 'exams'])->get();
        return view('results.classes', compact('classes'));
    }

    public function examsIndex()
    {
        $exams = Exam::with(['class', 'students'])->get();
        return view('results.exams', compact('exams'));
    }

    public function create()
    {
        $classes = Classes::all();
        return view('results.create', compact('classes'));
    }

    public function getStudentsByClass($classId)
    {
        $students = Student::where('class_id', $classId)->get();
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'mathematics_theory' => 'nullable|numeric|min:0|max:50',
            'mathematics_practical' => 'nullable|numeric|min:0|max:50',
            'programming_theory' => 'nullable|numeric|min:0|max:50',
            'programming_practical' => 'nullable|numeric|min:0|max:50',
            'oops_theory' => 'nullable|numeric|min:0|max:50',
            'oops_practical' => 'nullable|numeric|min:0|max:50',
            'data_structure_theory' => 'nullable|numeric|min:0|max:50',
            'data_structure_practical' => 'nullable|numeric|min:0|max:50',
            'organization_behavior_theory' => 'nullable|numeric|min:0|max:50',
            'organization_behavior_practical' => 'nullable|numeric|min:0|max:50',
        ]);

        $result = Result::create($request->all());

        return redirect()->route('results.students')
            ->with('success', 'Result created successfully.');
    }

    public function show(Result $result)
    {
        return view('results.show', compact('result'));
    }

    public function edit(Result $result)
    {
        $classes = Classes::all();
        return view('results.edit', compact('result', 'classes'));
    }

    public function update(Request $request, Result $result)
    {
        $request->validate([
            'mathematics_theory' => 'nullable|numeric|min:0|max:50',
            'mathematics_practical' => 'nullable|numeric|min:0|max:50',
            'programming_theory' => 'nullable|numeric|min:0|max:50',
            'programming_practical' => 'nullable|numeric|min:0|max:50',
            'oops_theory' => 'nullable|numeric|min:0|max:50',
            'oops_practical' => 'nullable|numeric|min:0|max:50',
            'data_structure_theory' => 'nullable|numeric|min:0|max:50',
            'data_structure_practical' => 'nullable|numeric|min:0|max:50',
            'organization_behavior_theory' => 'nullable|numeric|min:0|max:50',
            'organization_behavior_practical' => 'nullable|numeric|min:0|max:50',
        ]);

        $result->update($request->all());

        return redirect()->route('results.students')
            ->with('success', 'Result updated successfully.');
    }

    public function destroy(Result $result)
    {
        $result->delete();
        return redirect()->route('results.students')
            ->with('success', 'Result deleted successfully.');
    }

    public function analysisIndex()
    {
        $classPerformance = Classes::with(['students', 'exams'])
            ->get()
            ->map(function ($class) {
                $totalStudents = $class->students->count();
                $passedStudents = $class->students->filter(function ($student) {
                    return $student->exams->avg('pivot.grade') >= 40;
                })->count();

                return [
                    'class_name' => $class->class_name,
                    'section' => $class->section,
                    'total_students' => $totalStudents,
                    'passed_students' => $passedStudents,
                    'pass_percentage' => $totalStudents ? ($passedStudents / $totalStudents) * 100 : 0,
                    'average_grade' => $class->students->flatMap->exams->avg('pivot.grade')
                ];
            });

        $examPerformance = Exam::with(['students'])
            ->get()
            ->map(function ($exam) {
                $totalStudents = $exam->students->count();
                $passedStudents = $exam->students->filter(function ($student) use ($exam) {
                    return $student->pivot->grade >= $exam->passing_marks;
                })->count();

                return [
                    'exam_title' => $exam->title,
                    'subject' => $exam->subject,
                    'total_students' => $totalStudents,
                    'passed_students' => $passedStudents,
                    'pass_percentage' => $totalStudents ? ($passedStudents / $totalStudents) * 100 : 0,
                    'average_grade' => $exam->students->avg('pivot.grade')
                ];
            });

        return view('results.analysis', compact('classPerformance', 'examPerformance'));
    }

    public function addSubjectsForm()
    {
        $classes = Classes::where('is_active', true)->get();
        $subjects = Subject::where('status', true)->get();
        return view('results.add-subjects', compact('classes', 'subjects'));
    }

    public function addSubjectsToClass(Request $request, $classId)
    {
        $request->validate([
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id'
        ]);

        try {
            $class = Classes::findOrFail($classId);
            $class->subjects()->syncWithoutDetaching($request->subject_ids);
            
            return redirect()->back()->with('success', 'Subjects added to class successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add subjects to class. ' . $e->getMessage());
        }
    }

    public function addMarksForm()
    {
        $classes = Classes::all();
        $subjects = Subject::all();
        return view('results.add-marks', compact('classes', 'subjects'));
    }

    public function addMarks(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'marks' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0'
        ]);

        $mark = Mark::create($validatedData);
        return redirect()->back()->with('success', 'Marks added successfully.');
    }

    public function generateStudentMarksheet($studentId)
    {
        $student = Student::with(['marks.subject', 'class'])->findOrFail($studentId);
        
        $marksheet = [
            'student_name' => $student->name,
            'class' => $student->class->class_name,
            'subjects' => $student->marks->map(function($mark) {
                return [
                    'subject_name' => $mark->subject->name,
                    'marks' => $mark->marks,
                    'total_marks' => $mark->total_marks,
                    'percentage' => ($mark->marks / $mark->total_marks) * 100
                ];
            })
        ];

        return view('results.student-marksheet', compact('marksheet'));
    }

    public function generateClassMarksheet($classId)
    {
        $class = Classes::with(['students.marks.subject'])->findOrFail($classId);
        
        $classMarksheet = [
            'class_name' => $class->class_name,
            'students' => $class->students->map(function($student) {
                return [
                    'student_name' => $student->name,
                    'roll_number' => $student->roll_number,
                    'subjects' => $student->marks->map(function($mark) {
                        return [
                            'subject_name' => $mark->subject->name,
                            'marks' => $mark->marks,
                            'total_marks' => $mark->total_marks,
                            'percentage' => ($mark->marks / $mark->total_marks) * 100
                        ];
                    })
                ];
            })
        ];

        return view('results.class-marksheet', compact('classMarksheet'));
    }
}

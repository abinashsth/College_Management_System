<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamResult;
use App\Models\ExaminerAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarksEntryController extends Controller
{
    public function showEntryForm()
    {
        $user = Auth::user();
        $assignments = ExaminerAssignment::with(['class', 'subject'])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
            
        $exams = Exam::whereHas('class', function($query) use ($assignments) {
            $query->whereIn('id', $assignments->pluck('class_id'));
        })->get();
        
        return view('exam-management.marks.entry', compact('assignments', 'exams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
            'theory_marks' => 'required|numeric|min:0',
            'practical_marks' => 'nullable|numeric|min:0',
        ]);

        // Check if examiner is assigned to this class and subject
        $exam = Exam::findOrFail($validated['exam_id']);
        $student = Student::findOrFail($validated['student_id']);
        
        $isAssigned = ExaminerAssignment::where('user_id', Auth::id())
            ->where('class_id', $student->class_id)
            ->where('subject_id', $exam->subject_id)
            ->exists();
            
        if (!$isAssigned) {
            return back()->with('error', 'You are not authorized to enter marks for this student.');
        }

        // Calculate total marks and grade
        $totalMarks = $validated['theory_marks'] + ($validated['practical_marks'] ?? 0);
        $isPass = $totalMarks >= $exam->passing_marks;

        ExamResult::updateOrCreate(
            [
                'exam_id' => $validated['exam_id'],
                'student_id' => $validated['student_id'],
                'subject_id' => $exam->subject_id
            ],
            [
                'theory_marks' => $validated['theory_marks'],
                'practical_marks' => $validated['practical_marks'],
                'total_marks' => $totalMarks,
                'is_pass' => $isPass
            ]
        );

        return back()->with('success', 'Marks entered successfully.');
    }

    public function showBatchForm()
    {
        $user = Auth::user();
        $assignments = ExaminerAssignment::with(['class', 'subject'])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
            
        $exams = Exam::whereHas('class', function($query) use ($assignments) {
            $query->whereIn('id', $assignments->pluck('class_id'));
        })->get();
        
        return view('exam-management.marks.batch-entry', compact('assignments', 'exams'));
    }

    public function storeBatch(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.theory_marks' => 'required|numeric|min:0',
            'marks.*.practical_marks' => 'nullable|numeric|min:0',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        
        // Check authorization
        $isAssigned = ExaminerAssignment::where('user_id', Auth::id())
            ->where('class_id', $exam->class_id)
            ->where('subject_id', $exam->subject_id)
            ->exists();
            
        if (!$isAssigned) {
            return back()->with('error', 'You are not authorized to enter marks for this exam.');
        }

        DB::beginTransaction();
        try {
            foreach ($request->marks as $mark) {
                $totalMarks = $mark['theory_marks'] + ($mark['practical_marks'] ?? 0);
                $isPass = $totalMarks >= $exam->passing_marks;

                ExamResult::updateOrCreate(
                    [
                        'exam_id' => $request->exam_id,
                        'student_id' => $mark['student_id'],
                        'subject_id' => $exam->subject_id
                    ],
                    [
                        'theory_marks' => $mark['theory_marks'],
                        'practical_marks' => $mark['practical_marks'] ?? null,
                        'total_marks' => $totalMarks,
                        'is_pass' => $isPass
                    ]
                );
            }
            DB::commit();
            return back()->with('success', 'Marks entered successfully for all students.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error entering marks. Please try again.');
        }
    }
}

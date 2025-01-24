<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;

class ResultController extends Controller
{
    public function studentResults(Student $student): JsonResponse
    {
        $results = $student->exams()
            ->with('class')
            ->get()
            ->map(function ($exam) {
                return [
                    'exam' => $exam->title,
                    'subject' => $exam->subject,
                    'date' => $exam->exam_date,
                    'total_marks' => $exam->total_marks,
                    'grade' => $exam->pivot->grade,
                    'remarks' => $exam->pivot->remarks,
                    'status' => $exam->pivot->grade >= $exam->passing_marks ? 'Pass' : 'Fail'
                ];
            });

        return response()->json([
            'student' => $student->name,
            'class' => $student->class->class_name,
            'results' => $results
        ]);
    }

    public function classResults(Classes $class): JsonResponse
    {
        $results = $class->students()
            ->with(['exams' => function ($query) {
                $query->orderBy('exam_date', 'desc');
            }])
            ->get()
            ->map(function ($student) {
                return [
                    'student_name' => $student->name,
                    'exams' => $student->exams->map(function ($exam) {
                        return [
                            'exam' => $exam->title,
                            'subject' => $exam->subject,
                            'grade' => $exam->pivot->grade,
                            'status' => $exam->pivot->grade >= $exam->passing_marks ? 'Pass' : 'Fail'
                        ];
                    })
                ];
            });

        return response()->json([
            'class' => $class->class_name,
            'section' => $class->section,
            'results' => $results
        ]);
    }

    public function examResults(Exam $exam): JsonResponse
    {
        $results = $exam->students()
            ->with('class')
            ->get()
            ->map(function ($student) {
                return [
                    'student_name' => $student->name,
                    'class' => $student->class->class_name,
                    'grade' => $student->pivot->grade,
                    'remarks' => $student->pivot->remarks,
                    'status' => $student->pivot->grade >= $exam->passing_marks ? 'Pass' : 'Fail'
                ];
            });

        $stats = [
            'total_students' => $results->count(),
            'passed' => $results->where('status', 'Pass')->count(),
            'failed' => $results->where('status', 'Fail')->count(),
            'average_grade' => $results->avg('grade'),
            'highest_grade' => $results->max('grade'),
            'lowest_grade' => $results->min('grade')
        ];

        return response()->json([
            'exam' => $exam->title,
            'subject' => $exam->subject,
            'date' => $exam->exam_date,
            'total_marks' => $exam->total_marks,
            'passing_marks' => $exam->passing_marks,
            'statistics' => $stats,
            'results' => $results
        ]);
    }
}

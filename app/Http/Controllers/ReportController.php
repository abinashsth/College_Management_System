<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Exam;
use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function studentReport()
    {
        $students = Student::with(['class', 'exams'])->get();
        return view('reports.student', compact('students'));
    }

    public function studentReportApi(Student $student)
    {
        $student->load(['class', 'exams.subject', 'exams.examType']);
        return view('reports.partials.student-report', compact('student'))->render();
    }

    public function classIndex()
    {
        $classes = Classes::withCount(['students', 'exams'])
            ->with(['exams.students'])
            ->get();
        return view('reports.class-list', compact('classes'));
    }

    public function classReport(Classes $class)
    {
        $class->load(['students', 'exams.subject', 'exams.examType']);
        return view('reports.class', compact('class'));
    }

    public function subjectIndex()
    {
        $subjects = Subject::withCount('exams')
            ->with(['exams.students'])
            ->get();
        return view('reports.subject-list', compact('subjects'));
    }

    public function subjectShow(Subject $subject)
    {
        $subject->load(['exams.students', 'exams.class', 'exams.examType']);
        return view('reports.subject', compact('subject'));
    }

    public function subjectReport()
    {
        $subjects = Subject::with(['exams'])->get();
        return view('reports.subject', compact('subjects'));
    }

    public function examIndex()
    {
        $exams = Exam::with(['class', 'subject', 'examType', 'students'])
            ->latest('exam_date')
            ->get();
        return view('reports.exam-list', compact('exams'));
    }

    public function examShow(Exam $exam)
    {
        $exam->load(['class', 'subject', 'examType', 'students']);
        return view('reports.exam', compact('exam'));
    }

    public function examReport()
    {
        $exams = Exam::with(['class', 'students', 'subject', 'examType'])->get();
        return view('reports.exam', compact('exams'));
    }

    public function downloadStudentReport(Student $student)
    {
        $student->load(['class', 'exams.subject', 'exams.examType']);
        $pdf = PDF::loadView('reports.downloads.student', compact('student'));
        return $pdf->download("student_report_{$student->id}.pdf");
    }

    public function downloadClassReport(Classes $class)
    {
        $class->load(['students', 'exams.subject', 'exams.examType']);
        $pdf = PDF::loadView('reports.downloads.class', compact('class'));
        return $pdf->download("class_report_{$class->id}.pdf");
    }

    public function downloadSubjectReport(Subject $subject)
    {
        $subject->load(['exams.class', 'exams.students']);
        $pdf = PDF::loadView('reports.downloads.subject', compact('subject'));
        return $pdf->download("subject_report_{$subject->id}.pdf");
    }

    public function downloadExamReport(Exam $exam)
    {
        $exam->load(['class', 'students', 'subject', 'examType']);
        $pdf = PDF::loadView('reports.downloads.exam', compact('exam'));
        return $pdf->download("exam_report_{$exam->id}.pdf");
    }
}

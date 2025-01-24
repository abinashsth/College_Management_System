<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamResult;
use App\Models\ExaminerAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamResultController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole(['super-admin', 'admin'])) {
            $results = ExamResult::with(['exam', 'student', 'subject'])
                ->latest()
                ->paginate(15);
        } else {
            $assignedClasses = ExaminerAssignment::where('user_id', $user->id)
                ->pluck('class_id');
                
            $results = ExamResult::whereHas('student', function($query) use ($assignedClasses) {
                $query->whereIn('class_id', $assignedClasses);
            })->with(['exam', 'student', 'subject'])
            ->latest()
            ->paginate(15);
        }
        
        return view('exam-management.results.index', compact('results'));
    }

    public function examResults(Exam $exam)
    {
        $this->authorize('view', $exam);
        
        $results = ExamResult::with(['student', 'subject'])
            ->where('exam_id', $exam->id)
            ->get();
            
        return view('exam-management.results.exam-results', compact('exam', 'results'));
    }

    public function studentResults(Student $student)
    {
        $this->authorize('view', $student);
        
        $results = ExamResult::with(['exam', 'subject'])
            ->where('student_id', $student->id)
            ->get()
            ->groupBy('exam_id');
            
        return view('exam-management.results.student-results', compact('student', 'results'));
    }

    public function examSummary(Exam $exam)
    {
        $this->authorize('view', $exam);
        
        $summary = DB::table('exam_results')
            ->where('exam_id', $exam->id)
            ->select(
                DB::raw('COUNT(*) as total_students'),
                DB::raw('COUNT(CASE WHEN is_pass = 1 THEN 1 END) as passed_students'),
                DB::raw('COUNT(CASE WHEN is_pass = 0 THEN 1 END) as failed_students'),
                DB::raw('AVG(total_marks) as average_marks'),
                DB::raw('MAX(total_marks) as highest_marks'),
                DB::raw('MIN(total_marks) as lowest_marks')
            )
            ->first();
            
        return view('exam-management.results.exam-summary', compact('exam', 'summary'));
    }

    public function studentMarksheet(Student $student)
    {
        $this->authorize('view', $student);
        
        $results = ExamResult::with(['exam', 'subject'])
            ->where('student_id', $student->id)
            ->get()
            ->groupBy('exam_id');
            
        return view('exam-management.results.student-marksheet', compact('student', 'results'));
    }

    public function calculateRanks(Exam $exam)
    {
        $this->authorize('update', $exam);
        
        DB::transaction(function() use ($exam) {
            $results = ExamResult::where('exam_id', $exam->id)
                ->orderByDesc('total_marks')
                ->get();
                
            $currentRank = 1;
            $previousMarks = null;
            $sameRankCount = 0;
            
            foreach ($results as $result) {
                if ($previousMarks !== null && $previousMarks != $result->total_marks) {
                    $currentRank += $sameRankCount;
                    $sameRankCount = 1;
                } else {
                    $sameRankCount++;
                }
                
                $result->update(['rank' => $currentRank]);
                $previousMarks = $result->total_marks;
            }
        });
        
        return back()->with('success', 'Ranks calculated successfully.');
    }
}

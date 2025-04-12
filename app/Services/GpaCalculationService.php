<?php

namespace App\Services;

use App\Models\GradeRule;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;

class GpaCalculationService
{
    /**
     * Get the grade rule that applies to a specific percentage
     * 
     * @param float $percentage The percentage to find a grade rule for
     * @return GradeRule|null
     */
    public function getGradeRuleForPercentage(float $percentage): ?GradeRule
    {
        return GradeRule::where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();
    }

    /**
     * Calculate GPA for a collection of marks
     * 
     * @param Collection $marks Collection of Mark models
     * @return array Returns an array with 'gpa', 'total_credits', 'grade_points', and 'passed'
     */
    public function calculateGpa(Collection $marks): array
    {
        $totalCredits = 0;
        $totalGradePoints = 0;
        $allPassed = true;

        foreach ($marks as $mark) {
            // Skip if mark is absent
            if ($mark->is_absent) {
                continue;
            }

            $subject = $mark->subject;
            $percentage = $this->calculatePercentage($mark);
            $gradeRule = $this->getGradeRuleForPercentage($percentage);
            
            // If no grade rule found or student failed, mark as not passed
            if (!$gradeRule || !$gradeRule->isPassing()) {
                $allPassed = false;
            }

            if ($gradeRule) {
                $credits = $subject->credit_hours ?? 1;
                $totalCredits += $credits;
                $totalGradePoints += ($gradeRule->gpa * $credits);
            }
        }

        $gpa = $totalCredits > 0 ? ($totalGradePoints / $totalCredits) : 0;
        
        return [
            'gpa' => round($gpa, 2),
            'total_credits' => $totalCredits,
            'grade_points' => $totalGradePoints,
            'passed' => $allPassed
        ];
    }

    /**
     * Calculate percentage for a mark
     * 
     * @param Mark $mark The mark to calculate percentage for
     * @return float
     */
    public function calculatePercentage(Mark $mark): float
    {
        $exam = $mark->exam;
        $totalMarks = $exam->total_marks ?? 100;
        
        if ($totalMarks <= 0) {
            return 0;
        }
        
        return ($mark->marks_obtained / $totalMarks) * 100;
    }

    /**
     * Get grade information for a single mark
     * 
     * @param Mark $mark The mark to grade
     * @return array Returns array with 'percentage', 'grade', 'gpa', 'passed'
     */
    public function gradeMarkEntry(Mark $mark): array
    {
        $percentage = $this->calculatePercentage($mark);
        $gradeRule = $this->getGradeRuleForPercentage($percentage);
        
        return [
            'percentage' => round($percentage, 2),
            'grade' => $gradeRule ? $gradeRule->grade : 'N/A',
            'gpa' => $gradeRule ? $gradeRule->gpa : 0,
            'passed' => $gradeRule ? $gradeRule->isPassing() : false
        ];
    }

    /**
     * Generate a full result sheet for a student including all exams
     * 
     * @param Student $student The student to generate results for
     * @param int|null $classId Optional class ID to filter by
     * @return array
     */
    public function generateStudentResultSheet(Student $student, ?int $classId = null): array
    {
        $query = Mark::where('student_id', $student->id)
            ->whereHas('exam', function ($query) use ($classId) {
                $query->where('status', 'published');
                if ($classId) {
                    $query->where('class_id', $classId);
                }
            })
            ->with(['exam', 'subject']);
        
        $marks = $query->get();
        
        // Group marks by exam
        $examResults = [];
        foreach ($marks as $mark) {
            $examId = $mark->exam_id;
            if (!isset($examResults[$examId])) {
                $examResults[$examId] = [
                    'exam' => $mark->exam,
                    'marks' => [],
                    'gpa' => 0,
                    'passed' => true,
                    'total_percentage' => 0
                ];
            }
            
            $examResults[$examId]['marks'][] = $mark;
        }
        
        // Calculate GPA for each exam
        foreach ($examResults as $examId => &$result) {
            $marksCollection = collect($result['marks']);
            $gpaResult = $this->calculateGpa($marksCollection);
            $result['gpa'] = $gpaResult['gpa'];
            $result['passed'] = $gpaResult['passed'];
            
            // Calculate overall percentage
            $totalObtained = $marksCollection->sum('marks_obtained');
            $totalPossible = $marksCollection->sum(function ($mark) {
                return $mark->exam->total_marks ?? 100;
            });
            
            $result['total_percentage'] = $totalPossible > 0 
                ? round(($totalObtained / $totalPossible) * 100, 2) 
                : 0;
        }
        
        return $examResults;
    }
} 
<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Classes;
use App\Models\ExamType;
use App\Models\ExamResult;
use App\Models\AttendanceRecord;
use App\Models\StudentEcaMark;
use App\Models\TerminalMarksLedger;
use App\Models\FinalGradeSheet;
use App\Models\AcademicSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ResultCompilationService
{
    public function compileTerminalResults(
        Classes $class,
        ExamType $examType,
        AcademicSession $academicSession
    ): Collection {
        return DB::transaction(function () use ($class, $examType, $academicSession) {
            $students = $class->students;
            $compiledResults = collect();

            foreach ($students as $student) {
                $examResults = ExamResult::where([
                    'student_id' => $student->id,
                    'exam_type_id' => $examType->id,
                    'academic_session_id' => $academicSession->id
                ])->get();

                $totalMarks = $examResults->sum('total_marks');
                $totalPossibleMarks = $examResults->sum(function ($result) {
                    return $result->exam->total_marks;
                });

                $percentage = ($totalMarks / $totalPossibleMarks) * 100;
                $grade = $this->calculateGrade($percentage);

                $terminalLedger = TerminalMarksLedger::create([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_session_id' => $academicSession->id,
                    'exam_type_id' => $examType->id,
                    'total_marks' => $totalMarks,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'remarks' => $this->generateRemarks($grade)
                ]);

                $compiledResults->push($terminalLedger);
            }

            // Calculate and update ranks
            $this->updateRanks($compiledResults);

            return $compiledResults;
        });
    }

    public function compileFinalResults(
        Classes $class,
        AcademicSession $academicSession
    ): Collection {
        return DB::transaction(function () use ($class, $academicSession) {
            $students = $class->students;
            $finalResults = collect();

            foreach ($students as $student) {
                // Get all terminal results
                $terminalResults = TerminalMarksLedger::where([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_session_id' => $academicSession->id
                ])->get();

                // Calculate attendance percentage
                $attendancePercentage = $this->calculateAttendancePercentage(
                    $student,
                    $class,
                    $academicSession
                );

                // Calculate ECA average
                $ecaAverage = $this->calculateEcaAverage(
                    $student,
                    $academicSession
                );

                // Calculate final percentage (weighted average of all terms)
                $finalPercentage = $terminalResults->avg('percentage');
                $finalGrade = $this->calculateGrade($finalPercentage);

                $finalGradeSheet = FinalGradeSheet::create([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_session_id' => $academicSession->id,
                    'total_marks' => $terminalResults->sum('total_marks'),
                    'percentage' => $finalPercentage,
                    'grade' => $finalGrade,
                    'attendance_percentage' => $attendancePercentage,
                    'eca_average' => $ecaAverage,
                    'remarks' => $this->generateFinalRemarks($finalGrade, $attendancePercentage, $ecaAverage)
                ]);

                $finalResults->push($finalGradeSheet);
            }

            // Calculate and update final ranks
            $this->updateRanks($finalResults);

            return $finalResults;
        });
    }

    private function calculateAttendancePercentage(
        Student $student,
        Classes $class,
        AcademicSession $academicSession
    ): float {
        $totalDays = AttendanceRecord::where([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_session_id' => $academicSession->id
        ])->count();

        $presentDays = AttendanceRecord::where([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_session_id' => $academicSession->id,
            'status' => 'present'
        ])->count();

        return $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;
    }

    private function calculateEcaAverage(
        Student $student,
        AcademicSession $academicSession
    ): float {
        $ecaMarks = StudentEcaMark::where([
            'student_id' => $student->id,
            'academic_session_id' => $academicSession->id
        ])->get();

        return $ecaMarks->avg('marks') ?? 0;
    }

    private function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        return 'F';
    }

    private function generateRemarks(string $grade): string
    {
        return match($grade) {
            'A+' => 'Outstanding Performance',
            'A' => 'Excellent Performance',
            'B+' => 'Very Good Performance',
            'B' => 'Good Performance',
            'C+' => 'Satisfactory Performance',
            'C' => 'Average Performance',
            'F' => 'Needs Improvement',
            default => 'No Remarks'
        };
    }

    private function generateFinalRemarks(
        string $grade,
        float $attendancePercentage,
        float $ecaAverage
    ): string {
        $remarks = $this->generateRemarks($grade);

        if ($attendancePercentage < 75) {
            $remarks .= '. Attendance needs improvement';
        }

        if ($ecaAverage >= 80) {
            $remarks .= '. Excellent participation in extra-curricular activities';
        } elseif ($ecaAverage >= 60) {
            $remarks .= '. Good participation in extra-curricular activities';
        }

        return $remarks;
    }

    private function updateRanks(Collection $results): void
    {
        $rankedResults = $results->sortByDesc('percentage')->values();
        
        $currentRank = 1;
        $previousPercentage = null;
        $sameRankCount = 0;

        foreach ($rankedResults as $result) {
            if ($previousPercentage !== null && $previousPercentage != $result->percentage) {
                $currentRank += $sameRankCount;
                $sameRankCount = 1;
            } else {
                $sameRankCount++;
            }

            $result->update(['rank' => $currentRank]);
            $previousPercentage = $result->percentage;
        }
    }
} 
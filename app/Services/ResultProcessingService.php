<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\GradeSystem;
use App\Models\Mark;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResultProcessingService
{
    /**
     * Process results for a specific exam and student
     *
     * @param Exam $exam
     * @param Student $student
     * @return Result|null
     */
    public function processStudentResult(Exam $exam, Student $student)
    {
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Get all marks for this student in this exam
            $marks = Mark::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->where('is_verified', true)
                ->get();
            
            if ($marks->isEmpty()) {
                Log::info("No verified marks found for student {$student->id} in exam {$exam->id}");
                DB::rollBack();
                return null;
            }
            
            // Calculate total marks, obtained marks and percentage
            $totalSubjects = $marks->count();
            $totalMarks = $marks->sum(function ($mark) {
                return $mark->subject->full_marks;
            });
            
            $totalObtained = $marks->sum('marks_obtained');
            $percentage = ($totalObtained / $totalMarks) * 100;
            
            // Get active grade system
            $gradeSystem = GradeSystem::getActive();
            if (!$gradeSystem) {
                Log::error("No active grade system found");
                DB::rollBack();
                return null;
            }
            
            // Calculate GPA and grade
            $gpa = $gradeSystem->calculateGPA($percentage);
            $grade = $gradeSystem->getGrade($percentage);
            $isPassed = $gradeSystem->isPassing($percentage);
            
            // Create or update result
            $result = Result::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                ],
                [
                    'total_subjects' => $totalSubjects,
                    'total_marks' => $totalMarks,
                    'obtained_marks' => $totalObtained,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'gpa' => $gpa,
                    'is_passed' => $isPassed,
                    'grade_system_id' => $gradeSystem->id,
                    'processed_by' => auth()->id(),
                ]
            );
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing result: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Process results for all students in an exam
     *
     * @param Exam $exam
     * @return Collection
     */
    public function processExamResults(Exam $exam)
    {
        $results = collect();
        
        // Get unique students who have marks in this exam
        $studentIds = Mark::where('exam_id', $exam->id)
            ->where('is_verified', true)
            ->select('student_id')
            ->distinct()
            ->pluck('student_id');
            
        foreach ($studentIds as $studentId) {
            $student = Student::find($studentId);
            if ($student) {
                $result = $this->processStudentResult($exam, $student);
                if ($result) {
                    $results->push($result);
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Verify all results in an exam
     *
     * @param Exam $exam
     * @return bool
     */
    public function verifyExamResults(Exam $exam)
    {
        try {
            DB::beginTransaction();
            
            Result::where('exam_id', $exam->id)
                ->update([
                    'is_verified' => true,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
                
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error verifying results: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Publish results for an exam
     *
     * @param Exam $exam
     * @return bool
     */
    public function publishExamResults(Exam $exam)
    {
        try {
            DB::beginTransaction();
            
            Result::where('exam_id', $exam->id)
                ->where('is_verified', true)
                ->update([
                    'is_published' => true,
                    'published_by' => auth()->id(),
                    'published_at' => now(),
                ]);
                
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error publishing results: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get class-wise analysis for an exam
     *
     * @param Exam $exam
     * @return array
     */
    public function getClassAnalysis(Exam $exam)
    {
        $classResults = [];
        
        // Get results grouped by class
        $results = Result::with('student.studentRecord.section.class')
            ->where('exam_id', $exam->id)
            ->get()
            ->groupBy(function ($result) {
                return $result->student->studentRecord->section->class->id;
            });
            
        foreach ($results as $classId => $classResults) {
            $className = $classResults->first()->student->studentRecord->section->class->name;
            
            $totalStudents = $classResults->count();
            $passedStudents = $classResults->where('is_passed', true)->count();
            $failedStudents = $totalStudents - $passedStudents;
            $passPercentage = $totalStudents > 0 ? ($passedStudents / $totalStudents) * 100 : 0;
            
            $avgPercentage = $classResults->avg('percentage');
            $avgGPA = $classResults->avg('gpa');
            $maxPercentage = $classResults->max('percentage');
            $minPercentage = $classResults->min('percentage');
            
            $classAnalysis = [
                'class_id' => $classId,
                'class_name' => $className,
                'total_students' => $totalStudents,
                'passed_students' => $passedStudents,
                'failed_students' => $failedStudents,
                'pass_percentage' => $passPercentage,
                'avg_percentage' => $avgPercentage,
                'avg_gpa' => $avgGPA,
                'max_percentage' => $maxPercentage,
                'min_percentage' => $minPercentage,
            ];
            
            $classResults[$classId] = $classAnalysis;
        }
        
        return $classResults;
    }
    
    /**
     * Get subject-wise analysis for an exam
     *
     * @param Exam $exam
     * @return array
     */
    public function getSubjectAnalysis(Exam $exam)
    {
        $subjectResults = [];
        
        // Get all subjects for this exam
        $subjects = Subject::whereHas('marks', function ($query) use ($exam) {
            $query->where('exam_id', $exam->id);
        })->get();
        
        foreach ($subjects as $subject) {
            $marks = Mark::where('exam_id', $exam->id)
                ->where('subject_id', $subject->id)
                ->where('is_verified', true)
                ->get();
                
            $totalStudents = $marks->count();
            $passedStudents = $marks->filter(function ($mark) use ($subject) {
                return $mark->marks_obtained >= ($subject->pass_marks);
            })->count();
            
            $failedStudents = $totalStudents - $passedStudents;
            $passPercentage = $totalStudents > 0 ? ($passedStudents / $totalStudents) * 100 : 0;
            
            $avgMarks = $marks->avg('marks_obtained');
            $maxMarks = $marks->max('marks_obtained');
            $minMarks = $marks->min('marks_obtained');
            
            $subjectAnalysis = [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'full_marks' => $subject->full_marks,
                'pass_marks' => $subject->pass_marks,
                'total_students' => $totalStudents,
                'passed_students' => $passedStudents,
                'failed_students' => $failedStudents,
                'pass_percentage' => $passPercentage,
                'avg_marks' => $avgMarks,
                'max_marks' => $maxMarks,
                'min_marks' => $minMarks,
            ];
            
            $subjectResults[$subject->id] = $subjectAnalysis;
        }
        
        return $subjectResults;
    }
} 
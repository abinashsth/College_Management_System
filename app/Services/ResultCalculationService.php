<?php

namespace App\Services;

use App\Models\Result;
use App\Models\ResultDetail;
use App\Models\Mark;
use App\Models\GradeSystem;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResultCalculationService
{
    /**
     * The result being calculated.
     *
     * @var \App\Models\Result
     */
    protected $result;

    /**
     * The grade system to use for calculations.
     * 
     * @var \App\Models\GradeSystem
     */
    protected $gradeSystem;

    /**
     * Create a new result calculation service instance.
     *
     * @param \App\Models\Result $result
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
        $this->gradeSystem = $result->gradeSystem ?? GradeSystem::getDefault();
    }

    /**
     * Calculate the result based on associated marks.
     *
     * @return \App\Models\Result
     */
    public function calculate()
    {
        try {
            // Start transaction
            DB::beginTransaction();

            // Get all marks for this student in this exam
            $marks = Mark::where('student_id', $this->result->student_id)
                ->where('exam_id', $this->result->exam_id)
                ->where('status', 'published')
                ->get();

            // Delete existing result details
            ResultDetail::where('result_id', $this->result->id)->delete();

            $totalMarksObtained = 0;
            $totalMaxMarks = 0;
            $totalWeightedGradePoints = 0;
            $totalCreditHours = 0;
            $isPassed = true;

            // Create a result detail for each mark
            foreach ($marks as $mark) {
                $subject = Subject::find($mark->subject_id);
                $creditHours = $subject ? $subject->credit_hours : 1.0;

                $resultDetail = new ResultDetail([
                    'result_id' => $this->result->id,
                    'subject_id' => $mark->subject_id,
                    'mark_id' => $mark->id,
                    'credit_hours' => $creditHours,
                ]);

                $resultDetail->populateFromMark($mark, $creditHours);
                $resultDetail->save();

                // Update totals
                $totalMarksObtained += $mark->marks_obtained;
                $totalMaxMarks += $mark->total_marks;
                $totalWeightedGradePoints += $resultDetail->weighted_grade_point;
                $totalCreditHours += $creditHours;

                if (!$resultDetail->is_passed) {
                    $isPassed = false;
                }
            }

            // Calculate overall percentage
            $percentage = ($totalMaxMarks > 0) ? ($totalMarksObtained / $totalMaxMarks) * 100 : 0;

            // Calculate GPA
            $gpa = ($totalCreditHours > 0) ? $totalWeightedGradePoints / $totalCreditHours : 0;

            // Get grade letter based on GPA
            $grade = $this->getGradeFromGPA($gpa);

            // Update the result
            $this->result->update([
                'total_marks' => $totalMarksObtained,
                'percentage' => $percentage,
                'gpa' => $gpa,
                'grade' => $grade,
                'is_passed' => $isPassed,
                'calculated_by' => auth()->id(),
            ]);

            // Commit transaction
            DB::commit();

            return $this->result;
        } catch (\Exception $e) {
            // Rollback transaction if something goes wrong
            DB::rollBack();
            Log::error('Error calculating result: ' . $e->getMessage());
            
            // Rethrow the exception
            throw $e;
        }
    }

    /**
     * Get grade letter based on GPA.
     *
     * @param float $gpa
     * @return string
     */
    protected function getGradeFromGPA(float $gpa): string
    {
        if (!$this->gradeSystem) {
            return 'F';
        }

        $scale = $this->gradeSystem->scales()
            ->where('min_grade_point', '<=', $gpa)
            ->where('max_grade_point', '>=', $gpa)
            ->first();

        return $scale ? $scale->grade : 'F';
    }

    /**
     * Calculate class or section-wide results.
     *
     * @param int $examId
     * @param int $sectionId
     * @param int $userId
     * @return array
     */
    public static function calculateSectionResults(int $examId, int $sectionId, int $userId): array
    {
        $processed = 0;
        $failed = 0;

        try {
            // Get all students in the section
            $students = \App\Models\Student::where('section_id', $sectionId)
                ->where('enrollment_status', 'active')
                ->get();

            // Get the default grade system
            $gradeSystem = GradeSystem::getDefault();

            foreach ($students as $student) {
                try {
                    // Find or create result for this student
                    $result = Result::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'exam_id' => $examId,
                        ],
                        [
                            'grade_system_id' => $gradeSystem ? $gradeSystem->id : null,
                            'calculated_by' => $userId,
                        ]
                    );

                    // Calculate the result
                    $calculator = new self($result);
                    $calculator->calculate();
                    $processed++;
                } catch (\Exception $e) {
                    Log::error('Error calculating result for student #' . $student->id . ': ' . $e->getMessage());
                    $failed++;
                }
            }

            return [
                'success' => true,
                'processed' => $processed,
                'failed' => $failed,
                'message' => "Processed $processed results. Failed: $failed"
            ];
        } catch (\Exception $e) {
            Log::error('Error in batch result calculation: ' . $e->getMessage());
            return [
                'success' => false,
                'processed' => $processed,
                'failed' => $failed,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Batch verify results.
     *
     * @param int $examId
     * @param int $userId
     * @return array
     */
    public static function batchVerifyResults(int $examId, int $userId): array
    {
        $verified = 0;
        $failed = 0;

        try {
            $results = Result::where('exam_id', $examId)
                ->whereNull('verified_by')
                ->get();

            foreach ($results as $result) {
                try {
                    $result->verify($userId);
                    $verified++;
                } catch (\Exception $e) {
                    Log::error('Error verifying result #' . $result->id . ': ' . $e->getMessage());
                    $failed++;
                }
            }

            return [
                'success' => true,
                'verified' => $verified,
                'failed' => $failed,
                'message' => "Verified $verified results. Failed: $failed"
            ];
        } catch (\Exception $e) {
            Log::error('Error in batch result verification: ' . $e->getMessage());
            return [
                'success' => false,
                'verified' => $verified,
                'failed' => $failed,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Batch publish results.
     *
     * @param int $examId
     * @return array
     */
    public static function batchPublishResults(int $examId): array
    {
        $published = 0;
        $failed = 0;

        try {
            $results = Result::where('exam_id', $examId)
                ->whereNotNull('verified_by')
                ->whereNull('published_at')
                ->get();

            foreach ($results as $result) {
                try {
                    $result->publish();
                    $published++;
                } catch (\Exception $e) {
                    Log::error('Error publishing result #' . $result->id . ': ' . $e->getMessage());
                    $failed++;
                }
            }

            return [
                'success' => true,
                'published' => $published,
                'failed' => $failed,
                'message' => "Published $published results. Failed: $failed"
            ];
        } catch (\Exception $e) {
            Log::error('Error in batch result publishing: ' . $e->getMessage());
            return [
                'success' => false,
                'published' => $published,
                'failed' => $failed,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
} 
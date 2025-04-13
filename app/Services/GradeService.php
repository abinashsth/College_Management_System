<?php

namespace App\Services;

use App\Models\GradeRule;

class GradeService
{
    /**
     * Calculate GPA based on percentage
     *
     * @param float $percentage The student's percentage
     * @return array Array containing 'gpa' and 'grade'
     */
    public function calculateGPA(float $percentage): array
    {
        $gradeRule = $this->findGradeRule($percentage);

        if (!$gradeRule) {
            return [
                'gpa' => 0.0,
                'grade' => 'F',
                'status' => 'fail'
            ];
        }

        return [
            'gpa' => $gradeRule->gpa,
            'grade' => $gradeRule->grade,
            'status' => $percentage >= 40 ? 'pass' : 'fail' // Default pass threshold
        ];
    }

    /**
     * Find the grade rule for a given percentage
     *
     * @param float $percentage The student's percentage
     * @return GradeRule|null The matching grade rule or null if not found
     */
    protected function findGradeRule(float $percentage): ?GradeRule
    {
        return GradeRule::where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();
    }

    /**
     * Get grade letter from percentage
     *
     * @param float $percentage The student's percentage
     * @return string The grade letter
     */
    public function getGradeLetter(float $percentage): string
    {
        $gradeInfo = $this->calculateGPA($percentage);
        return $gradeInfo['grade'];
    }

    /**
     * Check if a student has passed based on percentage
     *
     * @param float $percentage The student's percentage
     * @param float $passThreshold Optional custom pass threshold
     * @return bool Whether the student has passed
     */
    public function isPassed(float $percentage, float $passThreshold = 40.0): bool
    {
        return $percentage >= $passThreshold;
    }

    /**
     * Generate performance category based on percentage
     *
     * @param float $percentage The student's percentage
     * @return string Performance category
     */
    public function getPerformanceCategory(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'Excellent';
        } elseif ($percentage >= 75) {
            return 'Very Good';
        } elseif ($percentage >= 60) {
            return 'Good';
        } elseif ($percentage >= 50) {
            return 'Satisfactory';
        } elseif ($percentage >= 40) {
            return 'Pass';
        } else {
            return 'Fail';
        }
    }
} 
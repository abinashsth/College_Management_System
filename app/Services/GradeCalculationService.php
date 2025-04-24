declare(strict_types=1);

namespace App\Services;

use App\Models\GradeSystem;

class GradeCalculationService
{
    public function calculateGrade(float $marks, float $maxMarks): array
    {
        $percentage = ($marks / $maxMarks) * 100;
        $gradeSystem = GradeSystem::getDefault();
        $grade = 'F';
        $gradePoint = 0.0;
        $remarks = 'Fail';

        foreach ($gradeSystem->scales as $scale) {
            if ($percentage >= $scale->min_percentage && $percentage <= $scale->max_percentage) {
                $grade = $scale->grade;
                $gradePoint = $scale->grade_point;
                $remarks = $scale->remarks;
                break;
            }
        }

        return [
            'grade' => $grade,
            'grade_point' => $gradePoint,
            'remarks' => $remarks,
            'percentage' => round($percentage, 2)
        ];
    }

    public function calculateGPA(array $marks): float
    {
        if (empty($marks)) {
            return 0.0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($marks as $mark) {
            $totalPoints += ($mark['grade_point'] * $mark['subject_credit']);
            $totalCredits += $mark['subject_credit'];
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }
} 
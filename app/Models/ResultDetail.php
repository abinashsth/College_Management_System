<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'result_id',
        'subject_id',
        'mark_id',
        'marks_obtained',
        'total_marks',
        'grade',
        'grade_point',
        'credit_hours',
        'weighted_grade_point',
        'is_absent',
        'is_passed',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'credit_hours' => 'decimal:2',
        'weighted_grade_point' => 'decimal:2',
        'is_absent' => 'boolean',
        'is_passed' => 'boolean',
    ];

    /**
     * Get the result that owns the detail.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class);
    }

    /**
     * Get the subject that owns the detail.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the mark that owns the detail.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mark(): BelongsTo
    {
        return $this->belongsTo(Mark::class);
    }

    /**
     * Calculate percentage based on obtained and total marks.
     *
     * @return float
     */
    public function calculatePercentage(): float
    {
        if ($this->total_marks > 0) {
            return ($this->marks_obtained / $this->total_marks) * 100;
        }
        
        return 0;
    }

    /**
     * Calculate grade point based on percentage.
     *
     * @return float|null
     */
    public function calculateGradePoint(): ?float
    {
        if ($this->is_absent) {
            return 0;
        }
        
        $percentage = $this->calculatePercentage();
        $gradeSystem = GradeSystem::getDefault();
        
        if ($gradeSystem) {
            $scale = $gradeSystem->scales()
                ->where('min_percentage', '<=', $percentage)
                ->where('max_percentage', '>=', $percentage)
                ->first();
            
            if ($scale) {
                return $scale->grade_point;
            }
        }
        
        return null;
    }

    /**
     * Calculate weighted grade point (grade point × credit hours).
     *
     * @return float|null
     */
    public function calculateWeightedGradePoint(): ?float
    {
        $gradePoint = $this->calculateGradePoint();
        
        if ($gradePoint !== null) {
            return $gradePoint * $this->credit_hours;
        }
        
        return null;
    }

    /**
     * Calculate and assign grade based on percentage.
     *
     * @return void
     */
    public function calculateGrade(): void
    {
        if ($this->is_absent) {
            $this->grade = 'AB';
            $this->grade_point = 0;
            $this->weighted_grade_point = 0;
            $this->is_passed = false;
            return;
        }
        
        $percentage = $this->calculatePercentage();
        $gradeSystem = GradeSystem::getDefault();
        
        if ($gradeSystem) {
            $scale = $gradeSystem->scales()
                ->where('min_percentage', '<=', $percentage)
                ->where('max_percentage', '>=', $percentage)
                ->first();
            
            if ($scale) {
                $this->grade = $scale->grade;
                $this->grade_point = $scale->grade_point;
                $this->weighted_grade_point = $scale->grade_point * $this->credit_hours;
                $this->is_passed = $percentage >= $gradeSystem->pass_percentage;
                return;
            }
        }
        
        // Default values if no grade system or scale found
        $this->grade = null;
        $this->grade_point = null;
        $this->weighted_grade_point = null;
        $this->is_passed = $percentage >= 40; // Default passing percentage
    }

    /**
     * Generate detail based on mark.
     *
     * @param Mark $mark
     * @param float $creditHours
     * @return void
     */
    public function populateFromMark(Mark $mark, float $creditHours = 1.0): void
    {
        $this->mark_id = $mark->id;
        $this->marks_obtained = $mark->marks_obtained;
        $this->total_marks = $mark->total_marks;
        $this->is_absent = $mark->is_absent;
        $this->remarks = $mark->remarks;
        $this->credit_hours = $creditHours;
        
        // Calculate grade, grade point, etc.
        $this->calculateGrade();
    }
} 
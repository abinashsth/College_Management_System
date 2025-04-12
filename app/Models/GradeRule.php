<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeRule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'grade',
        'description',
        'min_percentage',
        'max_percentage',
        'gpa',
        'is_pass',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'gpa' => 'decimal:2',
        'is_pass' => 'boolean',
    ];

    /**
     * Get the user who created this grade rule
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get a descriptive label for this grade rule
     *
     * @return string
     */
    public function getDescriptiveLabel(): string
    {
        return $this->grade . ' (' . $this->min_percentage . '% - ' . $this->max_percentage . '%) - GPA: ' . $this->gpa;
    }

    /**
     * Check if this grade represents a passing grade
     *
     * @return bool
     */
    public function isPassing(): bool
    {
        return $this->is_pass;
    }

    /**
     * Get the grade system that owns the grade rule.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gradeSystem()
    {
        return $this->belongsTo(GradeSystem::class);
    }

    /**
     * Check if a percentage falls within this grade rule's range.
     *
     * @param float $percentage
     * @return bool
     */
    public function isInRange($percentage)
    {
        return $percentage >= $this->min_percentage && $percentage <= $this->max_percentage;
    }

    /**
     * Get the grade for a given percentage.
     *
     * @param float $percentage
     * @return string|null
     */
    public static function getGrade($percentage, $gradeSystemId)
    {
        $rule = self::where('grade_system_id', $gradeSystemId)
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        return $rule ? $rule->grade : null;
    }

    /**
     * Get the grade point for a given percentage.
     *
     * @param float $percentage
     * @return float|null
     */
    public static function getGradePoint($percentage, $gradeSystemId)
    {
        $rule = self::where('grade_system_id', $gradeSystemId)
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        return $rule ? $rule->grade_point : null;
    }

    /**
     * Check if a given percentage is a pass.
     *
     * @param float $percentage
     * @return bool
     */
    public static function isPass($percentage, $gradeSystemId)
    {
        $rule = self::where('grade_system_id', $gradeSystemId)
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        return $rule ? $rule->is_pass : false;
    }
} 
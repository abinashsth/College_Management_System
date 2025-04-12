<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated This model is deprecated and will be removed in future versions.
 * Please use the GradeScale model instead. This model maintains compatibility
 * with existing code but new features should use GradeScale.
 */
class GradeRange extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'grade_system_id',
        'grade',
        'min_percentage',
        'max_percentage',
        'description',
        'grade_point',
        'is_pass',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_pass' => 'boolean',
    ];

    /**
     * Get the grade system that owns the grade range.
     * 
     * @deprecated Use GradeScale instead
     */
    public function gradeSystem()
    {
        return $this->belongsTo(GradeSystem::class);
    }

    /**
     * Check if a percentage falls within this grade range.
     *
     * @param float $percentage
     * @return bool
     * 
     * @deprecated Use GradeScale instead
     */
    public function containsPercentage($percentage)
    {
        return $percentage >= $this->min_percentage && $percentage <= $this->max_percentage;
    }

    /**
     * Get the range representation.
     *
     * @return string
     * 
     * @deprecated Use GradeScale instead
     */
    public function getRangeTextAttribute()
    {
        return "{$this->min_percentage}% - {$this->max_percentage}%";
    }
} 
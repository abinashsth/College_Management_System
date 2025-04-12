<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeScale extends Model
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
        'name',
        'min_percentage',
        'max_percentage',
        'grade_point',
        'remarks',
        'is_failing',
        'color_code',
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
        'is_failing' => 'boolean',
    ];

    /**
     * Get the grade system this scale belongs to.
     */
    public function gradeSystem(): BelongsTo
    {
        return $this->belongsTo(GradeSystem::class);
    }

    /**
     * Check if this grade scale represents a failing grade.
     */
    public function isFailing(): bool
    {
        return $this->is_failing;
    }

    /**
     * Format the grade scale for display.
     */
    public function getFormattedRange(): string
    {
        return $this->min_percentage . '% - ' . $this->max_percentage . '%';
    }

    /**
     * Get the formatted grade with point.
     */
    public function getFormattedGrade(): string
    {
        return $this->grade . ' (' . number_format($this->grade_point, 2) . ')';
    }
} 
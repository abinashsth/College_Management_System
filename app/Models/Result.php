<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\ResultCalculationService;

class Result extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'exam_id',
        'grade_system_id',
        'total_marks',
        'percentage',
        'gpa',
        'grade',
        'remarks',
        'is_passed',
        'calculated_by',
        'verified_by',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'gpa' => 'decimal:2',
        'is_passed' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the student that this result belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the exam that this result is for.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the grade system used for this result.
     */
    public function gradeSystem(): BelongsTo
    {
        return $this->belongsTo(GradeSystem::class);
    }

    /**
     * Get the user who calculated this result.
     */
    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    /**
     * Get the user who verified this result.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get all marks associated with this result.
     */
    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class, 'exam_id', 'exam_id')
            ->where('student_id', $this->student_id);
    }

    /**
     * Calculate the result based on associated marks.
     */
    public function calculate(): self
    {
        $calculator = new ResultCalculationService($this);
        return $calculator->calculate();
    }

    /**
     * Verify the result.
     *
     * @param int $userId The ID of the user verifying the result
     */
    public function verify(int $userId): self
    {
        $this->update([
            'verified_by' => $userId,
        ]);

        return $this;
    }

    /**
     * Publish the result.
     */
    public function publish(): self
    {
        $this->update([
            'published_at' => now(),
        ]);

        return $this;
    }

    /**
     * Check if the result is published.
     */
    public function isPublished(): bool
    {
        return !is_null($this->published_at);
    }

    /**
     * Check if the result is verified.
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_by);
    }

    /**
     * Get the formatted GPA with the corresponding grade.
     */
    public function getFormattedGpaAttribute(): string
    {
        return $this->gpa . ' (' . $this->grade . ')';
    }
} 
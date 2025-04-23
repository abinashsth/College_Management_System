<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assignment_id',
        'submitted_at',
        'submission_file_path',
        'submission_text',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'status',
        'is_late'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'is_late' => 'boolean',
        'score' => 'integer',
    ];

    /**
     * Get the student that this assignment submission belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the assignment that this submission is for.
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the user who graded this submission.
     */
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Check if the submission is late.
     * 
     * @return bool
     */
    public function checkIfLate()
    {
        if (!$this->submitted_at) {
            return false;
        }
        
        return $this->submitted_at->gt($this->assignment->due_date);
    }

    /**
     * Calculate the final score, accounting for late submission penalty if applicable.
     * 
     * @return int|null
     */
    public function getFinalScore()
    {
        if ($this->score === null) {
            return null;
        }
        
        if ($this->is_late && $this->assignment->allow_late_submission) {
            $penalty = $this->assignment->late_submission_penalty;
            return max(0, $this->score - $penalty);
        }
        
        return $this->score;
    }

    /**
     * Get the percentage score based on the assignment's max score.
     * 
     * @return float|null
     */
    public function getPercentageScore()
    {
        if ($this->score === null) {
            return null;
        }
        
        $maxScore = $this->assignment->max_score;
        if ($maxScore === 0) {
            return 0;
        }
        
        return ($this->getFinalScore() / $maxScore) * 100;
    }

    /**
     * Check if the submission is overdue (assignment due date has passed but not submitted).
     * 
     * @return bool
     */
    public function isOverdue()
    {
        return !$this->submitted_at && now()->gt($this->assignment->due_date);
    }

    /**
     * Mark the submission as submitted.
     * 
     * @param string|null $filePath
     * @param string|null $text
     * @return bool
     */
    public function markAsSubmitted($filePath = null, $text = null)
    {
        $this->submitted_at = now();
        $this->submission_file_path = $filePath;
        $this->submission_text = $text;
        $this->is_late = $this->checkIfLate();
        $this->status = $this->is_late ? 'late' : 'submitted';
        
        return $this->save();
    }

    /**
     * Grade the submission.
     * 
     * @param int $score
     * @param string|null $feedback
     * @param int|null $gradedBy
     * @return bool
     */
    public function grade($score, $feedback = null, $gradedBy = null)
    {
        $this->score = $score;
        $this->feedback = $feedback;
        $this->graded_by = $gradedBy ?? auth()->id();
        $this->graded_at = now();
        $this->status = 'graded';
        
        return $this->save();
    }

    /**
     * Return the submission to the student for revisions.
     * 
     * @param string $feedback
     * @return bool
     */
    public function returnForRevision($feedback)
    {
        $this->feedback = $feedback;
        $this->status = 'returned';
        
        return $this->save();
    }

    /**
     * Scope a query to only include submitted assignments.
     */
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    /**
     * Scope a query to only include graded assignments.
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('score');
    }

    /**
     * Scope a query to only include pending (not graded) assignments.
     */
    public function scopePending($query)
    {
        return $query->whereNotNull('submitted_at')->whereNull('score');
    }

    /**
     * Scope a query to only include late submissions.
     */
    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }
}

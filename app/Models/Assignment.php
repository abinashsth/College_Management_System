<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'max_score',
        'subject_id',
        'class_id',
        'academic_year_id',
        'created_by',
        'status',
        'allow_late_submission',
        'late_submission_penalty',
        'attachment_path'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'allow_late_submission' => 'boolean',
        'late_submission_penalty' => 'integer',
        'max_score' => 'integer',
    ];

    /**
     * Get the subject that the assignment belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class that the assignment belongs to.
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the academic year that the assignment belongs to.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the user that created the assignment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the students assigned to this assignment.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_assignments')
            ->withPivot(['submitted_at', 'submission_file_path', 'submission_text', 'score', 'feedback', 
                         'graded_by', 'graded_at', 'status', 'is_late'])
            ->withTimestamps();
    }

    /**
     * Get the student submissions for this assignment.
     */
    public function studentAssignments()
    {
        return $this->hasMany(StudentAssignment::class);
    }

    /**
     * Check if the assignment is overdue.
     */
    public function isOverdue()
    {
        return now()->gt($this->due_date);
    }

    /**
     * Calculate the average score for this assignment.
     */
    public function getAverageScore()
    {
        return $this->studentAssignments()
            ->whereNotNull('score')
            ->avg('score');
    }

    /**
     * Get the count of submissions.
     */
    public function getSubmissionCount()
    {
        return $this->studentAssignments()
            ->whereNotNull('submitted_at')
            ->count();
    }

    /**
     * Get the percentage of submissions.
     */
    public function getSubmissionPercentage()
    {
        $totalStudents = $this->students()->count();
        if ($totalStudents === 0) {
            return 0;
        }
        
        return ($this->getSubmissionCount() / $totalStudents) * 100;
    }

    /**
     * Get the count of graded submissions.
     */
    public function getGradedCount()
    {
        return $this->studentAssignments()
            ->whereNotNull('score')
            ->count();
    }

    /**
     * Scope a query to only include published assignments.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include active (not archived) assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'archived');
    }

    /**
     * Scope a query to only include upcoming assignments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>', now());
    }

    /**
     * Scope a query to only include overdue assignments.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    /**
     * Scope a query to only include assignments for a specific subject.
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope a query to only include assignments for a specific class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }
}

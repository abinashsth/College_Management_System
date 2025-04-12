<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exam_id',
        'student_id',
        'subject_id',
        'marks_obtained',
        'total_marks',
        'grade',
        'remarks',
        'status',
        'is_absent',
        'created_by',
        'updated_by',
        'verified_by',
        'verified_at',
        'submitted_at',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'is_absent' => 'boolean',
        'verified_at' => 'datetime',
        'submitted_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Get the exam that owns the mark.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the student that owns the mark.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject that owns the mark.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the user who created the mark.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the mark.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who verified the mark.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the components for the mark.
     */
    public function components()
    {
        return $this->hasMany(MarkComponent::class);
    }

    /**
     * Calculate the percentage of marks obtained.
     *
     * @return float
     */
    public function getPercentageAttribute()
    {
        if ($this->total_marks > 0) {
            return ($this->marks_obtained / $this->total_marks) * 100;
        }
        
        return 0;
    }

    /**
     * Check if the mark is a pass.
     *
     * @return bool
     */
    public function getIsPassAttribute()
    {
        return $this->exam && $this->marks_obtained >= $this->exam->passing_marks;
    }

    /**
     * Get mark status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Calculate marks based on components.
     *
     * @return void
     */
    public function calculateMarks()
    {
        if ($this->components->isEmpty()) {
            return;
        }

        $totalObtained = 0;
        $totalMarks = 0;

        foreach ($this->components as $component) {
            $weightedMarks = $component->marks_obtained * ($component->weight_percentage / 100);
            $weightedTotal = $component->total_marks * ($component->weight_percentage / 100);
            
            $totalObtained += $weightedMarks;
            $totalMarks += $weightedTotal;
        }

        $this->marks_obtained = $totalObtained;
        $this->total_marks = $totalMarks;
        $this->save();
    }

    /**
     * Submit the mark.
     *
     * @param int $userId
     * @return bool
     */
    public function submit(int $userId): bool
    {
        $this->status = 'submitted';
        $this->updated_by = $userId;
        $this->submitted_at = now();
        
        return $this->save();
    }

    /**
     * Verify the mark.
     *
     * @param int $userId
     * @return bool
     */
    public function verify(int $userId): bool
    {
        if ($this->status !== 'submitted') {
            return false;
        }
        
        $this->status = 'verified';
        $this->verified_by = $userId;
        $this->verified_at = now();
        
        return $this->save();
    }

    /**
     * Publish the mark.
     *
     * @param int $userId
     * @return bool
     */
    public function publish(int $userId): bool
    {
        if ($this->status !== 'verified') {
            return false;
        }
        
        $this->status = 'published';
        $this->updated_by = $userId;
        $this->published_at = now();
        
        return $this->save();
    }

    /**
     * Revert to draft status.
     *
     * @param int $userId
     * @return bool
     */
    public function revertToDraft(int $userId): bool
    {
        if ($this->status === 'published') {
            return false;
        }
        
        $this->status = 'draft';
        $this->updated_by = $userId;
        
        return $this->save();
    }

    /**
     * Scope a query to only include marks for a specific exam.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $examId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    /**
     * Scope a query to only include marks for a specific student.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include marks for a specific subject.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $subjectId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope a query to only include marks with a specific status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
} 
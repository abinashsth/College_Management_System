<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Mark extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'exam_id',
        'subject_id',
        'marks_obtained',
        'total_marks',
        'grade',
        'is_absent',
        'remarks',
        'status',
        'created_by',
        'updated_by',
        'verified_by',
        'published_by',
        'verification_date',
        'publication_date',
        'verification_remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_absent' => 'boolean',
        'verification_date' => 'datetime',
        'publication_date' => 'datetime',
    ];

    /**
     * Status values for marks
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_VERIFIED = 'verified';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the student that owns the mark.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the exam that owns the mark.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
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
     * Get the user who last updated the mark.
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
     * Get the user who published the mark.
     */
    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Get the components for this mark.
     */
    public function components()
    {
        return $this->hasMany(MarkComponent::class);
    }

    /**
     * Check if mark is passing.
     *
     * @return bool
     */
    public function isPassing()
    {
        if ($this->is_absent) {
            return false;
        }

        if ($this->marks_obtained === null) {
            return false;
        }

        $exam = $this->exam;
        return $this->marks_obtained >= $exam->passing_marks;
    }

    /**
     * Get mark percentage.
     *
     * @return float|null
     */
    public function getPercentage()
    {
        if ($this->is_absent || $this->marks_obtained === null) {
            return null;
        }

        $total = $this->total_marks ?? $this->exam->total_marks;
        return ($this->marks_obtained / $total) * 100;
    }

    /**
     * Calculate Grade based on marks.
     *
     * @return string|null
     */
    public function calculateGrade()
    {
        if ($this->is_absent) {
            return 'AB';
        }

        if ($this->marks_obtained === null) {
            return null;
        }

        $percentage = $this->getPercentage();
        if ($percentage === null) {
            return null;
        }

        // Find grade in grade system
        $gradeSystem = GradeSystem::getDefault();
        if (!$gradeSystem) {
            return null;
        }

        return $gradeSystem->findGradeForPercentage($percentage);
    }

    /**
     * Recalculate the marks based on components.
     *
     * @return bool
     */
    public function calculateMarks()
    {
        if ($this->is_absent) {
            $this->marks_obtained = null;
            $this->grade = 'AB';
            return $this->save();
        }

        $components = $this->components;
        if ($components->isEmpty()) {
            return false;
        }

        $totalMarks = 0;
        $weightSum = 0;

        foreach ($components as $component) {
            if ($component->marks_obtained !== null) {
                $weight = $component->weight_percentage / 100;
                $weightedMarks = ($component->marks_obtained / $component->total_marks) * $weight;
                $totalMarks += $weightedMarks;
                $weightSum += $weight;
            }
        }

        if ($weightSum > 0) {
            // Normalize to 100% in case weights don't sum to 100
            $totalMarks = ($totalMarks / $weightSum) * 100;
            
            // Scale to exam total marks
            $examTotalMarks = $this->exam->total_marks;
            $this->marks_obtained = round($totalMarks * $examTotalMarks / 100, 2);
            
            // Calculate grade
            $this->grade = $this->calculateGrade();
            
            return $this->save();
        }

        return false;
    }

    /**
     * Submit mark for verification.
     *
     * @param int $userId
     * @return bool
     */
    public function submit($userId = null)
    {
        if ($this->status !== self::STATUS_DRAFT && $this->status !== self::STATUS_REJECTED) {
            return false;
        }

        $this->status = self::STATUS_SUBMITTED;
        $this->updated_by = $userId ?? Auth::id();
        
        return $this->save();
    }

    /**
     * Verify the mark.
     *
     * @param int $userId
     * @return bool
     */
    public function verify($userId = null)
    {
        if ($this->status !== self::STATUS_SUBMITTED) {
            return false;
        }

        $this->status = self::STATUS_VERIFIED;
        $this->verified_by = $userId ?? Auth::id();
        $this->verification_date = now();
        
        return $this->save();
    }

    /**
     * Reject the mark verification.
     *
     * @param int $userId
     * @param string|null $remarks
     * @return bool
     */
    public function reject($userId = null, $remarks = null)
    {
        if ($this->status !== self::STATUS_SUBMITTED) {
            return false;
        }

        $this->status = self::STATUS_REJECTED;
        $this->updated_by = $userId ?? Auth::id();
        $this->verification_remarks = $remarks;
        
        return $this->save();
    }

    /**
     * Publish the mark.
     *
     * @param int $userId
     * @return bool
     */
    public function publish($userId = null)
    {
        if ($this->status !== self::STATUS_VERIFIED) {
            return false;
        }

        $this->status = self::STATUS_PUBLISHED;
        $this->published_by = $userId ?? Auth::id();
        $this->publication_date = now();
        
        return $this->save();
    }

    /**
     * Determine if mark can be edited.
     *
     * @return bool
     */
    public function canEdit()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * Determine if mark can be submitted.
     *
     * @return bool
     */
    public function canSubmit()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * Determine if mark can be verified.
     *
     * @return bool
     */
    public function canVerify()
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Determine if mark can be published.
     *
     * @return bool
     */
    public function canPublish()
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    /**
     * Get formatted status label with appropriate color class
     * 
     * @return array
     */
    public function getStatusInfo()
    {
        $label = ucfirst($this->status);
        $colorClass = 'gray';
        
        switch ($this->status) {
            case self::STATUS_DRAFT:
                $colorClass = 'blue';
                break;
            case self::STATUS_SUBMITTED:
                $colorClass = 'yellow';
                break;
            case self::STATUS_VERIFIED:
                $colorClass = 'green';
                break;
            case self::STATUS_PUBLISHED:
                $colorClass = 'indigo';
                break;
            case self::STATUS_REJECTED:
                $colorClass = 'red';
                break;
        }
        
        return [
            'label' => $label,
            'color' => $colorClass
        ];
    }
} 
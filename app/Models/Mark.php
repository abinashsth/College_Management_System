<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        'subject_id',
        'marks',
        'exam_date',
        'remarks',
    ];

    /**
     * Validation rules for the mark model.
     *
     * @var array
     */
    protected static $rules = [
        'marks_obtained' => 'nullable|numeric|min:0',
        'total_marks' => 'required|numeric|min:0',
        'grade' => 'nullable|string|max:5',
        'status' => 'required|in:draft,submitted,verified,published,rejected',
        'student_id' => 'required|exists:students,id',
        'exam_id' => 'required|exists:exams,id',
        'subject_id' => 'required|exists:subjects,id',
        'is_absent' => 'boolean',
        'remarks' => 'nullable|string|max:500',
        'verification_remarks' => 'nullable|string|max:500'
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
        'exam_date' => 'date',
        'marks' => 'float',
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
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($mark) {
            if (auth()->check()) {
                $mark->created_by = auth()->id();
                $mark->updated_by = auth()->id();
            }
        });

        static::updating(function ($mark) {
            if (auth()->check()) {
                $mark->updated_by = auth()->id();
            }

            // Log status changes
            if ($mark->isDirty('status')) {
                Log::info('Mark status changed', [
                    'mark_id' => $mark->id,
                    'student_id' => $mark->student_id,
                    'exam_id' => $mark->exam_id,
                    'subject_id' => $mark->subject_id,
                    'old_status' => $mark->getOriginal('status'),
                    'new_status' => $mark->status,
                    'user_id' => auth()->id(),
                    'timestamp' => now()
                ]);
            }

            // Validate status transition if status is changing
            if ($mark->isDirty('status')) {
                $mark->validateStatusTransition($mark->status);
            }
        });
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
        
        // If no grade system is found, use default grading scale
        if (!$gradeSystem) {
            return $this->getDefaultGrade($percentage);
        }

        $grade = $gradeSystem->findGradeForPercentage($percentage);
        return $grade ?? $this->getDefaultGrade($percentage);
    }

    /**
     * Get default grade based on percentage.
     *
     * @param float $percentage
     * @return string
     */
    protected function getDefaultGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        return 'F';
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

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public static function getValidationRules()
    {
        return static::$rules;
    }

    /**
     * Check if mark can transition to the given status.
     *
     * @param string $newStatus
     * @return bool
     */
    public function canTransitionTo($newStatus)
    {
        $allowedTransitions = [
            self::STATUS_DRAFT => [self::STATUS_SUBMITTED],
            self::STATUS_SUBMITTED => [self::STATUS_VERIFIED, self::STATUS_REJECTED],
            self::STATUS_VERIFIED => [self::STATUS_PUBLISHED],
            self::STATUS_REJECTED => [self::STATUS_SUBMITTED],
            self::STATUS_PUBLISHED => []
        ];
        
        return in_array($newStatus, $allowedTransitions[$this->status] ?? []);
    }

    /**
     * Validate status transition.
     *
     * @param string $newStatus
     * @throws \InvalidArgumentException
     */
    protected function validateStatusTransition($newStatus)
    {
        if (!$this->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Invalid status transition from {$this->status} to {$newStatus}"
            );
        }
    }

    /**
     * Bulk submit marks for verification.
     *
     * @param array $markIds
     * @return int Number of marks updated
     */
    public static function bulkSubmit(array $markIds)
    {
        return DB::transaction(function() use ($markIds) {
            $marks = static::whereIn('id', $markIds)
                ->where('status', self::STATUS_DRAFT)
                ->get();
            
            $count = 0;
            foreach ($marks as $mark) {
                if ($mark->submit()) {
                    $count++;
                }
            }
            
            return $count;
        });
    }

    /**
     * Bulk verify marks.
     *
     * @param array $markIds
     * @return int Number of marks verified
     */
    public static function bulkVerify(array $markIds)
    {
        return DB::transaction(function() use ($markIds) {
            $marks = static::whereIn('id', $markIds)
                ->where('status', self::STATUS_SUBMITTED)
                ->get();
            
            $count = 0;
            foreach ($marks as $mark) {
                if ($mark->verify()) {
                    $count++;
                }
            }
            
            return $count;
        });
    }

    /**
     * Bulk publish marks.
     *
     * @param array $markIds
     * @return int Number of marks published
     */
    public static function bulkPublish(array $markIds)
    {
        return DB::transaction(function() use ($markIds) {
            $marks = static::whereIn('id', $markIds)
                ->where('status', self::STATUS_VERIFIED)
                ->get();
            
            $count = 0;
            foreach ($marks as $mark) {
                if ($mark->publish()) {
                    $count++;
                }
            }
            
            return $count;
        });
    }

    /**
     * Calculate results for multiple marks.
     *
     * @param array $markIds
     * @return int Number of marks calculated
     */
    public static function bulkCalculateResults(array $markIds)
    {
        return DB::transaction(function() use ($markIds) {
            $marks = static::whereIn('id', $markIds)->get();
            
            $count = 0;
            foreach ($marks as $mark) {
                if ($mark->calculateResult()) {
                    $count++;
                }
            }
            
            return $count;
        });
    }

    /**
     * Calculate result for a single mark.
     *
     * @return bool
     */
    public function calculateResult()
    {
        return DB::transaction(function() {
            // Calculate marks from components if they exist
            if (!$this->calculateMarks()) {
                return false;
            }

            // Calculate grade
            $this->grade = $this->calculateGrade();
            
            // Save changes
            if (!$this->save()) {
                return false;
            }

            // Update student's overall result if this is a published mark
            if ($this->status === self::STATUS_PUBLISHED) {
                $this->student->calculateOverallResult();
            }
            
            return true;
        });
    }
} 
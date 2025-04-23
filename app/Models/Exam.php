<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'exam_date',
        'class_id',
        'subject_id',
        'academic_session_id',
        'exam_type',
        'semester',
        'duration_minutes',
        'start_time',
        'end_time',
        'location',
        'room_number',
        'total_marks',
        'passing_marks',
        'registration_deadline',
        'result_date',
        'is_published',
        'weight_percentage',
        'grading_scale',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'is_active' => 'boolean',
        'total_marks' => 'integer',
        'passing_marks' => 'integer',
        'duration_minutes' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'registration_deadline' => 'date',
        'result_date' => 'date',
        'is_published' => 'boolean',
        'weight_percentage' => 'decimal:2'
    ];

    /**
     * Get the class that this exam belongs to.
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the subject that this exam belongs to (legacy support).
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the subjects for this exam (many-to-many).
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'exam_subject')
            ->withPivot('total_marks', 'passing_marks', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the academic session that this exam belongs to.
     */
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the user who created this exam.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this exam.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the students for this exam with their grades.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'exam_student')
            ->withPivot('grade', 'remarks')
            ->withTimestamps();
    }

    /**
     * Get the schedules for this exam.
     */
    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    /**
     * Get the rules for this exam.
     */
    public function rules()
    {
        return $this->hasMany(ExamRule::class);
    }

    /**
     * Get the materials for this exam.
     */
    public function materials()
    {
        return $this->hasMany(ExamMaterial::class);
    }

    /**
     * Calculate the pass percentage of the exam.
     */
    public function passPercentage()
    {
        $totalStudents = $this->students()->count();
        if ($totalStudents === 0) {
            return 0;
        }
        
        $passedStudents = $this->students()
            ->wherePivot('grade', '>=', $this->passing_marks)
            ->count();
            
        return ($passedStudents / $totalStudents) * 100;
    }

    /**
     * Get the average grade for this exam.
     */
    public function averageGrade()
    {
        return $this->students()->avg('exam_student.grade') ?? 0;
    }

    /**
     * Check if the exam has started.
     */
    public function hasStarted()
    {
        return now() >= $this->exam_date . ' ' . $this->start_time;
    }

    /**
     * Check if the exam has ended.
     */
    public function hasEnded()
    {
        return now() >= $this->exam_date . ' ' . $this->end_time;
    }

    /**
     * Check if the exam is currently in progress.
     */
    public function isInProgress()
    {
        return $this->hasStarted() && !$this->hasEnded();
    }
} 
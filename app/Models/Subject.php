<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'slug',
        'description',
        'credit_hours',
        'lecture_hours',
        'practical_hours',
        'tutorial_hours',
        'level',
        'department_id',
        'semester_offered',
        'learning_objectives',
        'grading_policy',
        'syllabus',
        'reference_materials',
        'teaching_methods',
        'status',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'credit_hours' => 'integer',
        'lecture_hours' => 'integer',
        'practical_hours' => 'integer',
        'tutorial_hours' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($subject) {
            if (empty($subject->slug)) {
                $subject->slug = Str::slug($subject->name);
            }
        });
    }

    /**
     * Get the department that owns the subject.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the courses that include this subject.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'subject_course')
            ->withPivot('semester', 'year', 'is_core', 'status', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the teachers assigned to this subject.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teacher')
            ->withPivot('academic_session_id', 'role', 'teaching_hours_per_week', 
                         'start_date', 'end_date', 'is_coordinator', 'status', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the prerequisites for this subject.
     */
    public function prerequisites()
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisites', 'subject_id', 'prerequisite_id')
            ->withPivot('type', 'min_grade', 'description', 'status')
            ->withTimestamps();
    }

    /**
     * Get the subjects that require this subject as a prerequisite.
     */
    public function prerequisiteFor()
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisites', 'prerequisite_id', 'subject_id')
            ->withPivot('type', 'min_grade', 'description', 'status')
            ->withTimestamps();
    }

    /**
     * Get the programs that include this subject.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_subject', 'subject_id', 'program_id')
            ->withTimestamps();
    }

    /**
     * Get the academic sessions this subject is taught in.
     */
    public function academicSessions()
    {
        return $this->belongsToMany(AcademicSession::class, 'subject_teacher')
            ->withPivot('user_id', 'role', 'teaching_hours_per_week', 
                        'start_date', 'end_date', 'is_coordinator', 'status', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the coordinator for this subject in the current session.
     */
    public function coordinator()
    {
        return $this->teachers()
            ->wherePivot('is_coordinator', true)
            ->wherePivot('status', 'active')
            ->first();
    }

    /**
     * Get total hours (lecture + practical + tutorial)
     */
    public function getTotalHoursAttribute()
    {
        return ($this->lecture_hours ?? 0) + ($this->practical_hours ?? 0) + ($this->tutorial_hours ?? 0);
    }

    /**
     * Scope a query to only include active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include subjects for a specific department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter subjects by credit hours.
     */
    public function scopeByCreditHours($query, $creditHours)
    {
        return $query->where('credit_hours', $creditHours);
    }

    /**
     * Scope a query to filter subjects by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope a query to filter subjects by semester offered.
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester_offered', 'like', "%$semester%");
    }
}

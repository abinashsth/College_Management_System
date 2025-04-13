<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
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
        'lab_hours',
        'tutorial_hours',
        'level',
        'type',
        'department_id',
        'status',
        'learning_outcomes',
        'evaluation_criteria',
        'syllabus',
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
        'lab_hours' => 'integer',
        'tutorial_hours' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->name);
            }
        });
    }

    /**
     * Get the department that owns the course.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the programs that include this course.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_courses')
            ->withPivot('semester', 'year', 'is_elective', 'status')
            ->withTimestamps();
    }

    /**
     * Get the prerequisites for this course.
     */
    public function prerequisites()
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_id')
            ->withPivot('requirement_type', 'status', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the courses that require this course as a prerequisite.
     */
    public function prerequisiteFor()
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'prerequisite_id', 'course_id')
            ->withPivot('requirement_type', 'status', 'notes')
            ->withTimestamps();
    }

    /**
     * Get total hours (lecture + lab + tutorial)
     */
    public function getTotalHoursAttribute()
    {
        return ($this->lecture_hours ?? 0) + ($this->lab_hours ?? 0) + ($this->tutorial_hours ?? 0);
    }

    /**
     * Scope a query to only include active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include courses for a specific department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter courses by credit hours.
     */
    public function scopeByCreditHours($query, $creditHours)
    {
        return $query->where('credit_hours', $creditHours);
    }

    /**
     * Scope a query to filter courses by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Get the classes that have this course.
     */
    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_courses', 'course_id', 'class_id')
            ->withPivot('semester', 'year', 'is_active', 'notes')
            ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'academic_year_id',
        'department_id',
        'program_id',
        'capacity',
        'status',
        'description'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'class_courses', 'class_id', 'course_id')
            ->withPivot('semester', 'year', 'is_active', 'notes')
            ->withTimestamps();
    }
} 
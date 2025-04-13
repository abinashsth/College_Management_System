<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_name',
        'class_id',
        'capacity',
        'teacher_id',
        'start_time',
        'end_time',
        'status',
        'description',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => 'string',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        // Get all students assigned to the class that this section belongs to
        return $this->hasMany(Student::class, 'class_id', 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function classroomAllocations()
    {
        return $this->hasMany(ClassroomAllocation::class);
    }
}

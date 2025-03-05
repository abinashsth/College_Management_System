<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        'duration',
        'description',
        'faculty_id',
        'status'
    ];

    /**
     * Get the classes for the course.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class);
    }

    /**
     * Get the subjects for the course.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'academic_year_id',
        'type', // semester, term, etc.
        'start_date',
        'end_date',
        'is_current',
        'description',
        'registration_start_date',
        'registration_end_date',
        'class_start_date',
        'class_end_date',
        'exam_start_date',
        'exam_end_date',
        'result_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
        'class_start_date' => 'date',
        'class_end_date' => 'date',
        'exam_start_date' => 'date',
        'exam_end_date' => 'date',
        'result_date' => 'date',
    ];

    /**
     * Get the academic year that owns the session.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the full name of the session
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->academicYear->year_range . ')';
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'section_id',
        'subject_id',
        'attendance_date',
        'status',
        'check_in',
        'check_out',
        'taken_by',
        'remarks',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'status' => 'string',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function takenBy()
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('attendance_date', [$from, $to]);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by section.
     */
    public function scopeBySection($query, $section_id)
    {
        return $query->where('section_id', $section_id);
    }

    /**
     * Scope a query to filter by subject.
     */
    public function scopeBySubject($query, $subject_id)
    {
        return $query->where('subject_id', $subject_id);
    }

    /**
     * Get class for this attendance record.
     */
    public function class()
    {
        return $this->section ? $this->section->class() : null;
    }
}

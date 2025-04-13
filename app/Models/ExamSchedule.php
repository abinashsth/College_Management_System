<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'section_id',
        'exam_date',
        'start_time',
        'end_time',
        'location',
        'room_number',
        'seating_capacity',
        'is_rescheduled',
        'reschedule_reason',
        'status',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_rescheduled' => 'boolean',
        'seating_capacity' => 'integer',
    ];

    /**
     * Get the exam that this schedule belongs to.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the section that this schedule is for.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the class through the section.
     */
    public function class()
    {
        return $this->section ? $this->section->class() : null;
    }

    /**
     * Get the creator of this schedule.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of this schedule.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the supervisors assigned to this schedule.
     */
    public function supervisors()
    {
        return $this->hasMany(ExamSupervisor::class);
    }

    /**
     * Check if the schedule has started.
     */
    public function hasStarted()
    {
        return now() >= $this->exam_date . ' ' . $this->start_time;
    }

    /**
     * Check if the schedule has ended.
     */
    public function hasEnded()
    {
        return now() >= $this->exam_date . ' ' . $this->end_time;
    }

    /**
     * Check if the schedule is currently in progress.
     */
    public function isInProgress()
    {
        return $this->hasStarted() && !$this->hasEnded();
    }

    /**
     * Get all students who are eligible for this exam through the section.
     */
    public function students()
    {
        return $this->section 
            ? Student::where('class_id', $this->section->class_id)
                ->where('enrollment_status', 'active')
                ->get()
            : collect();
    }

    /**
     * Scope a query to only include schedules for a specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('exam_date', $date);
    }

    /**
     * Scope a query to only include upcoming schedules.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>=', now()->format('Y-m-d'))
            ->orderBy('exam_date')
            ->orderBy('start_time');
    }

    /**
     * Get the duration of the exam in minutes.
     */
    public function getDurationInMinutes()
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        return $end->diffInMinutes($start);
    }
}

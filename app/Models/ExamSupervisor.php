<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSupervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_schedule_id',
        'user_id',
        'role',
        'reporting_time',
        'leaving_time',
        'is_confirmed',
        'confirmation_time',
        'is_attended',
        'responsibilities',
        'notes',
        'assigned_by'
    ];

    protected $casts = [
        'reporting_time' => 'datetime',
        'leaving_time' => 'datetime',
        'is_confirmed' => 'boolean',
        'confirmation_time' => 'datetime',
        'is_attended' => 'boolean'
    ];

    /**
     * Get the exam schedule this supervisor is assigned to.
     */
    public function schedule()
    {
        return $this->belongsTo(ExamSchedule::class, 'exam_schedule_id');
    }

    /**
     * Get the user (teacher/staff) who is the supervisor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who assigned this supervision duty.
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the exam associated with this supervision through the schedule.
     */
    public function exam()
    {
        return $this->schedule ? $this->schedule->exam : null;
    }

    /**
     * Get all supervisor roles as an array for dropdown lists.
     */
    public static function getRoles()
    {
        return [
            'chief_supervisor' => 'Chief Supervisor',
            'supervisor' => 'Supervisor',
            'assistant_supervisor' => 'Assistant Supervisor',
            'invigilator' => 'Invigilator',
            'other' => 'Other'
        ];
    }

    /**
     * Scope a query to only include supervisors with a specific role.
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include supervisors who have confirmed.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('is_confirmed', true);
    }

    /**
     * Scope a query to only include supervisors who have not confirmed.
     */
    public function scopeUnconfirmed($query)
    {
        return $query->where('is_confirmed', false);
    }

    /**
     * Scope a query to get supervisors for a specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereHas('schedule', function($q) use ($date) {
            $q->where('exam_date', $date);
        });
    }

    /**
     * Mark this supervision as confirmed.
     */
    public function confirm()
    {
        $this->is_confirmed = true;
        $this->confirmation_time = now();
        $this->save();
        
        return $this;
    }

    /**
     * Mark this supervision as attended.
     */
    public function markAttended()
    {
        $this->is_attended = true;
        $this->save();
        
        return $this;
    }
}

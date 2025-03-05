<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentEvent extends Model
{
    protected $fillable = [
        'event_name',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'venue',
        'is_active'
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function participations(): HasMany
    {
        return $this->hasMany(StudentEventParticipation::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Student::class, 'student_event_participation')
            ->withPivot('participation_type', 'status', 'remarks')
            ->withTimestamps();
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'faculty_id',
        'title',
        'description',
        'location',
        'start_datetime',
        'end_datetime',
        'all_day',
        'type',
        'status',
        'created_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'all_day' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the faculty that owns the event.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the user who created the event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all attendees for this event.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'faculty_event_attendees')
            ->withPivot('status', 'response')
            ->withTimestamps();
    }

    /**
     * Check if event is upcoming.
     *
     * @return bool
     */
    public function isUpcoming()
    {
        return now()->lt($this->start_datetime);
    }

    /**
     * Check if event is ongoing.
     *
     * @return bool
     */
    public function isOngoing()
    {
        return now()->gte($this->start_datetime) && now()->lte($this->end_datetime);
    }

    /**
     * Check if event is past.
     *
     * @return bool
     */
    public function isPast()
    {
        return now()->gt($this->end_datetime);
    }
} 
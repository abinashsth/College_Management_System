<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassroomAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'floor',
        'building',
        'capacity',
        'type',
        'status',
        'section_id',
        'academic_session_id',
        'day',
        'start_time',
        'end_time',
        'description',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => 'string',
        'type' => 'string',
        'day' => 'string',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the class associated with this classroom allocation.
     */
    public function class()
    {
        return $this->section ? $this->section->class() : null;
    }

    /**
     * Check if the room is available for a given time slot.
     */
    public function isAvailable($day, $start_time, $end_time, $except_section_id = null)
    {
        $query = self::where('room_number', $this->room_number)
            ->where('day', $day)
            ->where('status', 'available')
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time]);
            });

        if ($except_section_id) {
            $query->where('section_id', '!=', $except_section_id);
        }

        return $query->count() === 0;
    }
}

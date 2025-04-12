<?php

namespace App\Services;

use App\Models\ClassroomAllocation;
use App\Models\Section;
use App\Models\User;
use App\Notifications\ClassroomAllocated;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ClassroomAllocationService
{
    /**
     * Allocate a classroom to a section.
     *
     * @param array $data
     * @return ClassroomAllocation
     */
    public function allocateClassroom(array $data): ClassroomAllocation
    {
        $allocation = ClassroomAllocation::create($data);
        
        // Get the section info
        $section = Section::findOrFail($data['section_id']);
        
        // Notify admin users and the teacher assigned to the section
        $admins = User::permission('manage classroom allocations')->get();
        
        if ($section->teacher_id) {
            $teacher = User::find($section->teacher_id);
            if ($teacher) {
                $teacher->notify(new ClassroomAllocated($allocation, $section));
            }
        }
        
        Notification::send($admins, new ClassroomAllocated($allocation, $section));
        
        return $allocation;
    }
    
    /**
     * Check if a classroom is available for a given time slot.
     *
     * @param string $roomNumber
     * @param string $day
     * @param string $startTime
     * @param string $endTime
     * @param int|null $exceptSectionId
     * @return bool
     */
    public function isClassroomAvailable(string $roomNumber, string $day, string $startTime, string $endTime, ?int $exceptSectionId = null): bool
    {
        $startTimeObj = Carbon::parse($startTime);
        $endTimeObj = Carbon::parse($endTime);
        
        $query = ClassroomAllocation::where('room_number', $roomNumber)
            ->where('day', $day)
            ->where('status', 'available')
            ->where(function ($query) use ($startTimeObj, $endTimeObj) {
                $query->whereBetween('start_time', [$startTimeObj, $endTimeObj])
                    ->orWhereBetween('end_time', [$startTimeObj, $endTimeObj])
                    ->orWhere(function ($query) use ($startTimeObj, $endTimeObj) {
                        $query->where('start_time', '<=', $startTimeObj)
                            ->where('end_time', '>=', $endTimeObj);
                    });
            });
        
        if ($exceptSectionId) {
            $query->where('section_id', '!=', $exceptSectionId);
        }
        
        return $query->count() === 0;
    }
    
    /**
     * Find all available classrooms for a given time slot.
     *
     * @param string $day
     * @param string $startTime
     * @param string $endTime
     * @param int $minCapacity
     * @param string|null $type
     * @return Collection
     */
    public function findAvailableClassrooms(string $day, string $startTime, string $endTime, int $minCapacity = 0, ?string $type = null): Collection
    {
        $startTimeObj = Carbon::parse($startTime);
        $endTimeObj = Carbon::parse($endTime);
        
        // Get all classrooms (unique room numbers)
        $allClassrooms = ClassroomAllocation::select('room_number', 'floor', 'building', 'capacity', 'type')
            ->when($type, function($query) use ($type) {
                return $query->where('type', $type);
            })
            ->where('capacity', '>=', $minCapacity)
            ->groupBy('room_number', 'floor', 'building', 'capacity', 'type')
            ->get();
        
        // Get all booked classrooms for the given time slot
        $bookedClassrooms = ClassroomAllocation::select('room_number')
            ->where('day', $day)
            ->where(function ($query) use ($startTimeObj, $endTimeObj) {
                $query->whereBetween('start_time', [$startTimeObj, $endTimeObj])
                    ->orWhereBetween('end_time', [$startTimeObj, $endTimeObj])
                    ->orWhere(function ($query) use ($startTimeObj, $endTimeObj) {
                        $query->where('start_time', '<=', $startTimeObj)
                            ->where('end_time', '>=', $endTimeObj);
                    });
            })
            ->pluck('room_number')
            ->toArray();
        
        // Filter out booked classrooms
        return $allClassrooms->filter(function ($classroom) use ($bookedClassrooms) {
            return !in_array($classroom->room_number, $bookedClassrooms);
        });
    }
    
    /**
     * Get all classroom allocations for a section.
     *
     * @param int $sectionId
     * @return Collection
     */
    public function getSectionSchedule(int $sectionId): Collection
    {
        return ClassroomAllocation::where('section_id', $sectionId)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();
    }
    
    /**
     * Get all classroom allocations for a room.
     *
     * @param string $roomNumber
     * @param string|null $building
     * @param int|null $floor
     * @return Collection
     */
    public function getRoomSchedule(string $roomNumber, ?string $building = null, ?int $floor = null): Collection
    {
        $query = ClassroomAllocation::where('room_number', $roomNumber);
        
        if ($building) {
            $query->where('building', $building);
        }
        
        if ($floor !== null) {
            $query->where('floor', $floor);
        }
        
        return $query->orderBy('day')
            ->orderBy('start_time')
            ->get();
    }
    
    /**
     * Get schedules for all classrooms on a specific day.
     *
     * @param string $day
     * @return array
     */
    public function getDailySchedule(string $day): array
    {
        // Get all allocations for the day
        $allocations = ClassroomAllocation::with(['section.class', 'section.teacher'])
            ->where('day', $day)
            ->orderBy('start_time')
            ->get();
        
        // Get all unique rooms
        $rooms = $allocations->pluck('room_number')->unique()->values()->toArray();
        
        // Create time slots (hourly from 8 AM to 8 PM)
        $timeSlots = [];
        for ($hour = 8; $hour <= 20; $hour++) {
            $timeSlots[] = Carbon::createFromTime($hour, 0, 0)->format('H:i');
        }
        
        // Create schedule data structure
        $schedule = [
            'day' => $day,
            'time_slots' => $timeSlots,
            'rooms' => $rooms,
            'schedule' => [],
        ];
        
        // Initialize schedule grid
        foreach ($rooms as $room) {
            $schedule['schedule'][$room] = [];
            foreach ($timeSlots as $timeSlot) {
                $schedule['schedule'][$room][$timeSlot] = null;
            }
        }
        
        // Fill in allocations
        foreach ($allocations as $allocation) {
            $startHour = Carbon::parse($allocation->start_time)->format('H');
            $endHour = Carbon::parse($allocation->end_time)->format('H');
            
            // Convert to integers for loop
            $startHourInt = (int) $startHour;
            $endHourInt = (int) $endHour;
            
            // Loop through each hour this allocation covers
            for ($hour = $startHourInt; $hour <= $endHourInt; $hour++) {
                $timeSlot = sprintf('%02d:00', $hour);
                
                // Only fill if the time slot exists in our grid
                if (in_array($timeSlot, $timeSlots)) {
                    $schedule['schedule'][$allocation->room_number][$timeSlot] = [
                        'allocation' => $allocation,
                        'section' => $allocation->section,
                        'class' => $allocation->section->class ?? null,
                        'teacher' => $allocation->section->teacher ?? null,
                    ];
                }
            }
        }
        
        return $schedule;
    }
} 
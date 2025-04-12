<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Section;
use App\Models\User;
use App\Exceptions\ExamException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service class for exam schedule operations.
 * 
 * This class provides methods for validating and managing exam schedules,
 * including checking for scheduling conflicts.
 */
class ExamScheduleService
{
    /**
     * Create a new exam schedule.
     *
     * @param array $data The schedule data
     * @return \App\Models\ExamSchedule
     * @throws \App\Exceptions\ExamException
     */
    public function createSchedule(array $data)
    {
        // Validate for scheduling conflicts
        $this->validateScheduleConflicts($data);
        
        // Use transaction to ensure data integrity
        return DB::transaction(function () use ($data) {
            return ExamSchedule::create($data);
        });
    }
    
    /**
     * Update an existing exam schedule.
     *
     * @param \App\Models\ExamSchedule $schedule The schedule to update
     * @param array $data The new schedule data
     * @return \App\Models\ExamSchedule
     * @throws \App\Exceptions\ExamException
     */
    public function updateSchedule(ExamSchedule $schedule, array $data)
    {
        // Validate for scheduling conflicts (excluding this schedule)
        $this->validateScheduleConflicts($data, $schedule->id);
        
        // Use transaction to ensure data integrity
        return DB::transaction(function () use ($schedule, $data) {
            $schedule->update($data);
            return $schedule;
        });
    }
    
    /**
     * Validate schedule for conflicts with existing schedules.
     *
     * @param array $data Schedule data
     * @param int|null $excludeScheduleId Schedule ID to exclude from conflict check
     * @return bool
     * @throws \App\Exceptions\ExamException
     */
    public function validateScheduleConflicts(array $data, ?int $excludeScheduleId = null)
    {
        $examDate = $data['exam_date'];
        $startTime = Carbon::parse($examDate . ' ' . $data['start_time']);
        $endTime = Carbon::parse($examDate . ' ' . $data['end_time']);
        $sectionId = $data['section_id'];
        
        // Check if end time is after start time
        if ($endTime <= $startTime) {
            throw ExamException::schedulingConflict(
                'Exam end time must be after start time',
                ['start_time' => $startTime, 'end_time' => $endTime]
            );
        }
        
        // Find any overlapping schedules for the same section
        $conflictQuery = ExamSchedule::where('section_id', $sectionId)
            ->where('exam_date', $examDate)
            ->where(function ($query) use ($startTime, $endTime) {
                // Overlap exists if:
                // (start_time <= existing_end_time) AND (end_time >= existing_start_time)
                $query->whereRaw('TIME(start_time) < TIME(?)', [$endTime->format('H:i:s')])
                    ->whereRaw('TIME(end_time) > TIME(?)', [$startTime->format('H:i:s')]);
            });
            
        // Exclude this schedule if updating
        if ($excludeScheduleId) {
            $conflictQuery->where('id', '!=', $excludeScheduleId);
        }
        
        $conflicts = $conflictQuery->get();
        
        if ($conflicts->count() > 0) {
            $conflict = $conflicts->first();
            $contextData = [
                'conflict_exam' => $conflict->exam->title,
                'conflict_date' => $conflict->exam_date,
                'conflict_time' => $conflict->start_time . ' - ' . $conflict->end_time,
                'section' => Section::find($sectionId)->name
            ];
            
            throw ExamException::schedulingConflict(
                'Scheduling conflict: The section already has an exam scheduled during this time',
                $contextData
            );
        }
        
        // Check for room conflicts if room is specified
        if (!empty($data['room_number'])) {
            $roomConflictQuery = ExamSchedule::where('room_number', $data['room_number'])
                ->where('exam_date', $examDate)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereRaw('TIME(start_time) < TIME(?)', [$endTime->format('H:i:s')])
                        ->whereRaw('TIME(end_time) > TIME(?)', [$startTime->format('H:i:s')]);
                });
                
            if ($excludeScheduleId) {
                $roomConflictQuery->where('id', '!=', $excludeScheduleId);
            }
            
            $roomConflicts = $roomConflictQuery->get();
            
            if ($roomConflicts->count() > 0) {
                $conflict = $roomConflicts->first();
                throw ExamException::schedulingConflict(
                    'Room scheduling conflict: The room is already booked for another exam during this time',
                    [
                        'conflict_exam' => $conflict->exam->title,
                        'conflict_date' => $conflict->exam_date,
                        'conflict_time' => $conflict->start_time . ' - ' . $conflict->end_time,
                        'room' => $data['room_number']
                    ]
                );
            }
        }
        
        return true;
    }
    
    /**
     * Check if a supervisor has availability for this schedule.
     *
     * @param int $userId User ID of the supervisor
     * @param array $scheduleData Schedule data
     * @param int|null $excludeScheduleId Schedule ID to exclude
     * @return bool
     * @throws \App\Exceptions\ExamException
     */
    public function validateSupervisorAvailability(int $userId, array $scheduleData, ?int $excludeScheduleId = null)
    {
        $examDate = $scheduleData['exam_date'];
        $startTime = Carbon::parse($examDate . ' ' . $scheduleData['start_time']);
        $endTime = Carbon::parse($examDate . ' ' . $scheduleData['end_time']);
        
        // Find schedules where this user is already assigned as a supervisor
        $conflictQuery = ExamSchedule::whereHas('supervisors', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('exam_date', $examDate)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereRaw('TIME(start_time) < TIME(?)', [$endTime->format('H:i:s')])
                    ->whereRaw('TIME(end_time) > TIME(?)', [$startTime->format('H:i:s')]);
            });
            
        if ($excludeScheduleId) {
            $conflictQuery->where('id', '!=', $excludeScheduleId);
        }
        
        $conflicts = $conflictQuery->get();
        
        if ($conflicts->count() > 0) {
            $conflict = $conflicts->first();
            $user = User::find($userId);
            
            throw ExamException::supervisionError(
                "Supervisor conflict: {$user->name} is already assigned to another exam during this time",
                [
                    'conflict_exam' => $conflict->exam->title,
                    'conflict_date' => $conflict->exam_date,
                    'conflict_time' => $conflict->start_time . ' - ' . $conflict->end_time,
                    'supervisor' => $user->name
                ]
            );
        }
        
        return true;
    }
    
    /**
     * Get a list of available supervisors for a given schedule.
     *
     * @param array $scheduleData Schedule data
     * @return \Illuminate\Support\Collection Users who are available
     */
    public function getAvailableSupervisors(array $scheduleData)
    {
        $examDate = $scheduleData['exam_date'];
        $startTime = Carbon::parse($examDate . ' ' . $scheduleData['start_time']);
        $endTime = Carbon::parse($examDate . ' ' . $scheduleData['end_time']);
        
        // Get users with teacher or admin roles
        $potentialSupervisors = User::role(['Teacher', 'Admin'])->get();
        
        // Filter out users who have scheduling conflicts
        return $potentialSupervisors->filter(function ($user) use ($examDate, $startTime, $endTime) {
            $conflicts = ExamSchedule::whereHas('supervisors', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('exam_date', $examDate)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereRaw('TIME(start_time) < TIME(?)', [$endTime->format('H:i:s')])
                        ->whereRaw('TIME(end_time) > TIME(?)', [$startTime->format('H:i:s')]);
                })
                ->count();
                
            return $conflicts === 0;
        });
    }
} 
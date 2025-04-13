<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AttendanceMarked;
use App\Notifications\AttendanceUpdated;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AttendanceService
{
    /**
     * Mark attendance for students in a section.
     *
     * @param array $data
     * @param User $takenBy
     * @return Collection
     */
    public function markAttendance(array $data, User $takenBy): Collection
    {
        $section_id = $data['section_id'];
        $subject_id = $data['subject_id'] ?? null;
        $attendance_date = $data['attendance_date'];
        $student_ids = $data['student_ids'];
        $statuses = $data['statuses'];
        $remarks = $data['remarks'] ?? [];
        
        // Get current time for check-in
        $check_in = Carbon::now()->format('H:i:s');
        
        // Get admin users to notify
        $admins = User::permission('view attendance')->get();
        
        $attendances = collect();
        
        // Use transaction to ensure all records are saved or none
        DB::beginTransaction();
        
        try {
            // Save or update attendance records
            foreach ($student_ids as $key => $student_id) {
                $status = $statuses[$key] ?? 'present';
                $remark = $remarks[$key] ?? null;
                
                // Check if a record already exists
                $attendance = Attendance::firstOrNew([
                    'student_id' => $student_id,
                    'section_id' => $section_id,
                    'subject_id' => $subject_id,
                    'attendance_date' => $attendance_date,
                ]);
                
                $isNew = !$attendance->exists;
                $oldStatus = $attendance->status;
                
                // Only set check-in time if it's a new record
                if ($isNew) {
                    $attendance->check_in = $check_in;
                }
                
                // Update other fields
                $attendance->status = $status;
                $attendance->remarks = $remark;
                $attendance->taken_by = $takenBy->id;
                
                $attendance->save();
                $attendances->push($attendance);
                
                // Send notification if it's a new attendance or status changed
                if ($isNew || $oldStatus !== $status) {
                    $student = Student::find($student_id);
                    
                    if ($student) {
                        // Notify student's guardian/parent if available
                        if ($student->user) {
                            if ($isNew) {
                                $student->user->notify(new AttendanceMarked($attendance, $student));
                            } else {
                                $student->user->notify(new AttendanceUpdated($attendance, $student, $oldStatus));
                            }
                        }
                        
                        // Notify admins
                        if ($isNew) {
                            Notification::send($admins, new AttendanceMarked($attendance, $student));
                        } else {
                            Notification::send($admins, new AttendanceUpdated($attendance, $student, $oldStatus));
                        }
                    }
                }
            }
            
            DB::commit();
            return $attendances;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Get attendance statistics for a section within a date range.
     *
     * @param Section $section
     * @param string|null $subject_id
     * @param string $from_date
     * @param string $to_date
     * @return array
     */
    public function getAttendanceStatistics(Section $section, ?string $subject_id, string $from_date, string $to_date): array
    {
        // Get all students from the class
        $students = Student::where('class_id', $section->class_id)
            ->where('enrollment_status', 'active')
            ->get();
        
        // Get all dates in the range
        $period = Carbon::parse($from_date)->daysUntil($to_date);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }
        
        // Get attendance records for the specified period
        $attendances = Attendance::where('section_id', $section->id)
            ->when($subject_id, function($query) use ($subject_id) {
                return $query->where('subject_id', $subject_id);
            })
            ->whereBetween('attendance_date', [$from_date, $to_date])
            ->get();
        
        // Organize attendance by student and date
        $attendanceData = [];
        
        foreach ($students as $student) {
            $attendanceData[$student->id] = [
                'student' => $student,
                'dates' => [],
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
                'sick_leave' => 0,
                'total' => count($dates),
            ];
            
            foreach ($dates as $date) {
                // Find attendance record for this student on this date
                $record = $attendances->first(function ($attendance) use ($student, $date) {
                    return $attendance->student_id == $student->id && $attendance->attendance_date->format('Y-m-d') == $date;
                });
                
                if ($record) {
                    $attendanceData[$student->id]['dates'][$date] = [
                        'status' => $record->status,
                        'remarks' => $record->remarks,
                    ];
                    
                    // Increment counters based on status
                    $attendanceData[$student->id][$record->status]++;
                } else {
                    $attendanceData[$student->id]['dates'][$date] = [
                        'status' => 'not_marked',
                        'remarks' => null,
                    ];
                }
            }
            
            // Calculate percentages
            $total = count($dates);
            $attendanceData[$student->id]['present_percentage'] = $total > 0 ? ($attendanceData[$student->id]['present'] / $total) * 100 : 0;
            $attendanceData[$student->id]['absent_percentage'] = $total > 0 ? ($attendanceData[$student->id]['absent'] / $total) * 100 : 0;
            $attendanceData[$student->id]['late_percentage'] = $total > 0 ? ($attendanceData[$student->id]['late'] / $total) * 100 : 0;
            $attendanceData[$student->id]['excused_percentage'] = $total > 0 ? ($attendanceData[$student->id]['excused'] / $total) * 100 : 0;
            $attendanceData[$student->id]['sick_leave_percentage'] = $total > 0 ? ($attendanceData[$student->id]['sick_leave'] / $total) * 100 : 0;
        }
        
        // Calculate overall statistics
        $overallStats = [
            'total_students' => count($students),
            'total_days' => count($dates),
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'sick_leave' => 0,
            'not_marked' => 0,
        ];
        
        $totalEntries = count($students) * count($dates);
        
        if ($totalEntries > 0) {
            // Sum up all attendance statuses
            foreach ($attendanceData as $studentData) {
                $overallStats['present'] += $studentData['present'];
                $overallStats['absent'] += $studentData['absent'];
                $overallStats['late'] += $studentData['late'];
                $overallStats['excused'] += $studentData['excused'];
                $overallStats['sick_leave'] += $studentData['sick_leave'];
            }
            
            // Calculate not marked entries
            $markedEntries = $overallStats['present'] + $overallStats['absent'] + $overallStats['late'] + $overallStats['excused'] + $overallStats['sick_leave'];
            $overallStats['not_marked'] = $totalEntries - $markedEntries;
            
            // Calculate percentages
            $overallStats['present_percentage'] = ($overallStats['present'] / $totalEntries) * 100;
            $overallStats['absent_percentage'] = ($overallStats['absent'] / $totalEntries) * 100;
            $overallStats['late_percentage'] = ($overallStats['late'] / $totalEntries) * 100;
            $overallStats['excused_percentage'] = ($overallStats['excused'] / $totalEntries) * 100;
            $overallStats['sick_leave_percentage'] = ($overallStats['sick_leave'] / $totalEntries) * 100;
            $overallStats['not_marked_percentage'] = ($overallStats['not_marked'] / $totalEntries) * 100;
        }
        
        return [
            'students' => $attendanceData,
            'dates' => $dates,
            'overall' => $overallStats,
        ];
    }
    
    /**
     * Get attendance report for a student.
     *
     * @param Student $student
     * @param string $from_date
     * @param string $to_date
     * @param int|null $section_id
     * @param int|null $subject_id
     * @return array
     */
    public function getStudentAttendanceReport(Student $student, string $from_date, string $to_date, ?int $section_id = null, ?int $subject_id = null): array
    {
        // Get all dates in the range
        $period = Carbon::parse($from_date)->daysUntil($to_date);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }
        
        // Get attendance records for the specified period
        $query = Attendance::where('student_id', $student->id)
            ->whereBetween('attendance_date', [$from_date, $to_date]);
            
        if ($section_id) {
            $query->where('section_id', $section_id);
        }
        
        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }
        
        $attendances = $query->with(['section.class', 'subject', 'takenBy'])->get();
        
        // Group attendances by date
        $attendanceByDate = [];
        
        foreach ($dates as $date) {
            $attendanceByDate[$date] = $attendances->filter(function ($attendance) use ($date) {
                return $attendance->attendance_date->format('Y-m-d') == $date;
            })->values();
        }
        
        // Calculate statistics
        $stats = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'excused' => $attendances->where('status', 'excused')->count(),
            'sick_leave' => $attendances->where('status', 'sick_leave')->count(),
            'total' => $attendances->count(),
        ];
        
        if ($stats['total'] > 0) {
            $stats['present_percentage'] = ($stats['present'] / $stats['total']) * 100;
            $stats['absent_percentage'] = ($stats['absent'] / $stats['total']) * 100;
            $stats['late_percentage'] = ($stats['late'] / $stats['total']) * 100;
            $stats['excused_percentage'] = ($stats['excused'] / $stats['total']) * 100;
            $stats['sick_leave_percentage'] = ($stats['sick_leave'] / $stats['total']) * 100;
        } else {
            $stats['present_percentage'] = 0;
            $stats['absent_percentage'] = 0;
            $stats['late_percentage'] = 0;
            $stats['excused_percentage'] = 0;
            $stats['sick_leave_percentage'] = 0;
        }
        
        return [
            'student' => $student,
            'dates' => $dates,
            'attendance_by_date' => $attendanceByDate,
            'stats' => $stats,
        ];
    }
} 
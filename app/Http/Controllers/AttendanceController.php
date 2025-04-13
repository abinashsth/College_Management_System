<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view attendance'])->only(['index', 'show', 'report']);
        $this->middleware(['auth', 'permission:create attendance'])->only(['create', 'store', 'mark']);
        $this->middleware(['auth', 'permission:edit attendance'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete attendance'])->only(['destroy']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $sections = Section::with('class')->where('status', 'active')->get();
        $attendances = Attendance::with(['student', 'section.class', 'subject'])
            ->where('attendance_date', $today)
            ->paginate(15);
        
        return view('attendances.index', compact('attendances', 'sections', 'today'));
    }

    /**
     * Show form for marking attendance.
     */
    public function create()
    {
        $sections = Section::with('class')->where('status', 'active')->get();
        $subjects = Subject::all();
        $today = Carbon::today()->format('Y-m-d');
        
        return view('attendances.create', compact('sections', 'subjects', 'today'));
    }

    /**
     * Show the students in the section for marking attendance.
     */
    public function mark(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'attendance_date' => 'required|date',
        ]);
        
        $section = Section::with('class')->findOrFail($request->section_id);
        $subject = $request->subject_id ? Subject::findOrFail($request->subject_id) : null;
        $date = $request->attendance_date;
        
        $students = Student::where('class_id', $section->class_id)
            ->where('enrollment_status', 'active')
            ->get();
        
        $existingAttendances = Attendance::where('section_id', $section->id)
            ->where('attendance_date', $date)
            ->when($subject, function($query) use ($subject) {
                return $query->where('subject_id', $subject->id);
            })
            ->get()
            ->keyBy('student_id');
        
        return view('attendances.mark', compact('section', 'subject', 'date', 'students', 'existingAttendances'));
    }

    /**
     * Store attendance records.
     */
    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'attendance_date' => 'required|date',
            'students' => 'required|array',
            'students.*.id' => 'required|exists:students,id',
            'students.*.status' => 'required|in:present,absent,late,excused,sick_leave',
            'students.*.remarks' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $section = Section::findOrFail($request->section_id);
            $subject = $request->subject_id ? Subject::findOrFail($request->subject_id) : null;
            $date = $request->attendance_date;
            
            foreach ($request->students as $studentData) {
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $studentData['id'],
                        'section_id' => $section->id,
                        'subject_id' => $subject ? $subject->id : null,
                        'attendance_date' => $date,
                    ],
                    [
                        'status' => $studentData['status'],
                        'remarks' => $studentData['remarks'] ?? null,
                        'marked_by' => auth()->id(),
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->route('attendances.index')
                ->with('success', 'Attendance marked successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while marking attendance: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return view('attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        return view('attendances.edit', compact('attendance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:present,absent,late,excused,sick_leave',
            'remarks' => 'nullable|string',
        ]);
        
        $attendance->update($request->all());
        
        return redirect()->route('attendances.index')
            ->with('success', 'Attendance has been updated successfully');
    }

    /**
     * Remove the attendance record.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        
        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record deleted successfully');
    }
    
    /**
     * Generate attendance report.
     */
    public function report(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);
        
        $section = Section::with('class')->findOrFail($request->section_id);
        $subject = $request->subject_id ? Subject::findOrFail($request->subject_id) : null;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        
        $students = Student::where('class_id', $section->class_id)
            ->where('enrollment_status', 'active')
            ->get();
        
        $attendances = Attendance::where('section_id', $section->id)
            ->when($subject, function($query) use ($subject) {
                return $query->where('subject_id', $subject->id);
            })
            ->whereBetween('attendance_date', [$from_date, $to_date])
            ->get();
        
        $attendanceData = [];
        $dates = [];
        
        $period = Carbon::parse($from_date)->daysUntil($to_date);
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }
        
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
                $record = $attendances->first(function ($attendance) use ($student, $date) {
                    return $attendance->student_id == $student->id && $attendance->attendance_date == $date;
                });
                
                if ($record) {
                    $attendanceData[$student->id]['dates'][$date] = $record->status;
                    $attendanceData[$student->id][$record->status]++;
                } else {
                    $attendanceData[$student->id]['dates'][$date] = null;
                }
            }
        }
        
        return view('attendances.report', compact('attendanceData', 'dates', 'section', 'subject', 'from_date', 'to_date'));
    }
} 
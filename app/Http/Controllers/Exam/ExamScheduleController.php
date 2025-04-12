<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExamSchedule::with(['exam', 'section']);
        
        // Filter by upcoming, if specified
        if ($request->has('upcoming') && $request->upcoming) {
            $query->upcoming();
        }
        
        // Filter by exam
        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }
        
        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('exam_date', $request->date);
        }
        
        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->where('section_id', $request->section_id);
        }
        
        $schedules = $query->orderBy('exam_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(10);
        
        $exams = Exam::where('is_active', true)->get();
        $sections = Section::all();
        
        return view('exam.schedules.index', compact('schedules', 'exams', 'sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $exams = Exam::where('is_active', true)->get();
        $sections = Section::all();
        
        return view('exam.schedules.create', compact('exams', 'sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'section_id' => 'required|exists:sections,id',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'room_number' => 'required|string|max:50',
            'seating_capacity' => 'required|integer|min:1',
            'is_rescheduled' => 'boolean',
            'reschedule_reason' => 'nullable|required_if:is_rescheduled,1|string|max:500',
            'status' => 'required|in:scheduled,in-progress,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Check if a schedule for this exam and section already exists
        $existingSchedule = ExamSchedule::where('exam_id', $request->exam_id)
            ->where('section_id', $request->section_id)
            ->exists();
            
        if ($existingSchedule) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A schedule for this exam and section already exists.');
        }
        
        $validatedData['created_by'] = Auth::id();
        
        DB::beginTransaction();
        
        try {
            $schedule = ExamSchedule::create($validatedData);
            
            DB::commit();
            
            return redirect()->route('exam.schedules.show', $schedule)
                ->with('success', 'Exam schedule created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create exam schedule. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamSchedule $schedule)
    {
        $schedule->load(['exam', 'section', 'supervisors.user', 'creator']);
        
        // Get eligible students for this exam schedule
        $eligibleStudents = $schedule->getEligibleStudents();
        
        return view('exam.schedules.show', compact('schedule', 'eligibleStudents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamSchedule $schedule)
    {
        $exams = Exam::where('is_active', true)->get();
        $sections = Section::all();
        
        return view('exam.schedules.edit', compact('schedule', 'exams', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamSchedule $schedule)
    {
        $validatedData = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'section_id' => [
                'required',
                'exists:sections,id',
                Rule::unique('exam_schedules')
                    ->where(function ($query) use ($request) {
                        return $query->where('exam_id', $request->exam_id);
                    })
                    ->ignore($schedule->id)
            ],
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'room_number' => 'required|string|max:50',
            'seating_capacity' => 'required|integer|min:1',
            'is_rescheduled' => 'boolean',
            'reschedule_reason' => 'nullable|required_if:is_rescheduled,1|string|max:500',
            'status' => 'required|in:scheduled,in-progress,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $validatedData['updated_by'] = Auth::id();
        
        DB::beginTransaction();
        
        try {
            $schedule->update($validatedData);
            
            DB::commit();
            
            return redirect()->route('exam.schedules.show', $schedule)
                ->with('success', 'Exam schedule updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update exam schedule. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamSchedule $schedule)
    {
        // Check if there are any supervisors assigned to this schedule
        if ($schedule->supervisors()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete schedule because supervisors are assigned. Remove all supervisors first.');
        }
        
        try {
            $schedule->delete();
            
            return redirect()->route('exam.schedules.index')
                ->with('success', 'Exam schedule deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete exam schedule. ' . $e->getMessage());
        }
    }
    
    /**
     * Assign supervisors to the exam schedule.
     */
    public function assignSupervisors(Request $request, ExamSchedule $schedule)
    {
        $validatedData = $request->validate([
            'supervisors' => 'required|array',
            'supervisors.*.user_id' => 'required|exists:users,id',
            'supervisors.*.role' => 'required|in:supervisor,invigilator,examiner,coordinator',
            'supervisors.*.responsibilities' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            foreach ($validatedData['supervisors'] as $supervisorData) {
                // Check if this supervisor is already assigned
                $existingSupervisor = $schedule->supervisors()
                    ->where('user_id', $supervisorData['user_id'])
                    ->first();
                
                if ($existingSupervisor) {
                    // Update existing supervisor
                    $existingSupervisor->update([
                        'role' => $supervisorData['role'],
                        'responsibilities' => $supervisorData['responsibilities'] ?? null,
                        'assigned_by' => Auth::id(),
                    ]);
                } else {
                    // Create new supervisor assignment
                    $schedule->supervisors()->create([
                        'user_id' => $supervisorData['user_id'],
                        'role' => $supervisorData['role'],
                        'responsibilities' => $supervisorData['responsibilities'] ?? null,
                        'assigned_by' => Auth::id(),
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('exam.schedules.show', $schedule)
                ->with('success', 'Supervisors assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to assign supervisors. ' . $e->getMessage());
        }
    }
    
    /**
     * Remove a supervisor from the exam schedule.
     */
    public function removeSupervisor(ExamSchedule $schedule, $supervisorId)
    {
        try {
            $supervisor = $schedule->supervisors()->findOrFail($supervisorId);
            $supervisor->delete();
            
            return redirect()->route('exam.schedules.show', $schedule)
                ->with('success', 'Supervisor removed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to remove supervisor. ' . $e->getMessage());
        }
    }
    
    /**
     * Change the status of an exam schedule.
     */
    public function changeStatus(Request $request, ExamSchedule $schedule)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:scheduled,in-progress,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);
        
        try {
            $schedule->update([
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes'] ?? $schedule->notes,
                'updated_by' => Auth::id(),
            ]);
            
            return redirect()->route('exam.schedules.show', $schedule)
                ->with('success', 'Exam schedule status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update exam schedule status. ' . $e->getMessage());
        }
    }
}

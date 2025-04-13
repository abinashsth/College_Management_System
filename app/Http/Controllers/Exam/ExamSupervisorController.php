<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamSchedule;
use App\Models\ExamSupervisor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamSupervisorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExamSupervisor::with(['schedule.exam', 'user', 'assigner']);
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by exam schedule
        if ($request->has('exam_schedule_id') && $request->exam_schedule_id) {
            $query->where('exam_schedule_id', $request->exam_schedule_id);
        }
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        // Filter by confirmation status
        if ($request->has('is_confirmed')) {
            $query->where('is_confirmed', $request->boolean('is_confirmed'));
        }
        
        // Filter by attendance status
        if ($request->has('is_attended')) {
            $query->where('is_attended', $request->boolean('is_attended'));
        }
        
        // Filter by upcoming exams
        if ($request->has('upcoming')) {
            $query->whereHas('schedule', function ($q) {
                $q->whereDate('exam_date', '>=', Carbon::today());
            });
        }
        
        $supervisors = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $schedules = ExamSchedule::with('exam')->get();
        $users = User::role(['teacher', 'admin', 'staff'])->get();
        $roles = ExamSupervisor::getRoles();
        
        return view('exam.supervisors.index', compact('supervisors', 'schedules', 'users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schedules = ExamSchedule::with('exam')->whereDate('exam_date', '>=', Carbon::today())->get();
        $users = User::role(['teacher', 'admin', 'staff'])->get();
        $roles = ExamSupervisor::getRoles();
        
        return view('exam.supervisors.create', compact('schedules', 'users', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:supervisor,invigilator,examiner,coordinator',
            'reporting_time' => 'required|date_format:H:i',
            'leaving_time' => 'required|date_format:H:i|after:reporting_time',
            'responsibilities' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Check if this supervisor is already assigned to this schedule
        $existingSupervisor = ExamSupervisor::where('exam_schedule_id', $request->exam_schedule_id)
            ->where('user_id', $request->user_id)
            ->exists();
            
        if ($existingSupervisor) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This user is already assigned to this exam schedule.');
        }
        
        // Get the schedule date
        $schedule = ExamSchedule::findOrFail($request->exam_schedule_id);
        $examDate = $schedule->exam_date;
        
        // Format reporting and leaving times with the exam date
        $validatedData['reporting_time'] = Carbon::parse($examDate->format('Y-m-d') . ' ' . $validatedData['reporting_time']);
        $validatedData['leaving_time'] = Carbon::parse($examDate->format('Y-m-d') . ' ' . $validatedData['leaving_time']);
        
        DB::beginTransaction();
        
        try {
            $validatedData['assigned_by'] = Auth::id();
            $validatedData['is_confirmed'] = false;
            $validatedData['is_attended'] = false;
            
            $supervisor = ExamSupervisor::create($validatedData);
            
            DB::commit();
            
            return redirect()->route('exam.supervisors.show', $supervisor)
                ->with('success', 'Exam supervisor assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to assign exam supervisor. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamSupervisor $supervisor)
    {
        $supervisor->load(['schedule.exam', 'user', 'assigner']);
        
        // Get other supervisors for the same exam
        $otherSupervisors = ExamSupervisor::where('exam_schedule_id', $supervisor->exam_schedule_id)
            ->where('id', '!=', $supervisor->id)
            ->with('user')
            ->get();
        
        return view('exam.supervisors.show', compact('supervisor', 'otherSupervisors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamSupervisor $supervisor)
    {
        $schedules = ExamSchedule::with('exam')->get();
        $users = User::role(['teacher', 'admin', 'staff'])->get();
        $roles = ExamSupervisor::getRoles();
        
        return view('exam.supervisors.edit', compact('supervisor', 'schedules', 'users', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamSupervisor $supervisor)
    {
        $validatedData = $request->validate([
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:supervisor,invigilator,examiner,coordinator',
            'reporting_time' => 'required|date_format:H:i',
            'leaving_time' => 'required|date_format:H:i|after:reporting_time',
            'responsibilities' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
            'is_confirmed' => 'boolean',
            'is_attended' => 'boolean',
        ]);
        
        // Check if this supervisor is already assigned to this schedule (excluding current record)
        $existingSupervisor = ExamSupervisor::where('exam_schedule_id', $request->exam_schedule_id)
            ->where('user_id', $request->user_id)
            ->where('id', '!=', $supervisor->id)
            ->exists();
            
        if ($existingSupervisor) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This user is already assigned to this exam schedule.');
        }
        
        // Get the schedule date
        $schedule = ExamSchedule::findOrFail($request->exam_schedule_id);
        $examDate = $schedule->exam_date;
        
        // Format reporting and leaving times with the exam date
        $validatedData['reporting_time'] = Carbon::parse($examDate->format('Y-m-d') . ' ' . $validatedData['reporting_time']);
        $validatedData['leaving_time'] = Carbon::parse($examDate->format('Y-m-d') . ' ' . $validatedData['leaving_time']);
        
        // Set boolean values
        $validatedData['is_confirmed'] = $request->has('is_confirmed');
        $validatedData['is_attended'] = $request->has('is_attended');
        
        // Set confirmation time if changed to confirmed
        if ($validatedData['is_confirmed'] && !$supervisor->is_confirmed) {
            $validatedData['confirmation_time'] = Carbon::now();
        }
        
        DB::beginTransaction();
        
        try {
            $supervisor->update($validatedData);
            
            DB::commit();
            
            return redirect()->route('exam.supervisors.show', $supervisor)
                ->with('success', 'Exam supervisor updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update exam supervisor. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamSupervisor $supervisor)
    {
        try {
            $supervisor->delete();
            
            return redirect()->route('exam.supervisors.index')
                ->with('success', 'Exam supervisor removed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to remove exam supervisor. ' . $e->getMessage());
        }
    }
    
    /**
     * Confirm assignment as a supervisor.
     */
    public function confirm(ExamSupervisor $supervisor)
    {
        // Check if the current user is the assigned supervisor
        if (Auth::id() != $supervisor->user_id) {
            return redirect()->back()
                ->with('error', 'You can only confirm your own supervision assignments.');
        }
        
        try {
            $supervisor->markAsConfirmed();
            
            return redirect()->route('exam.supervisors.show', $supervisor)
                ->with('success', 'You have confirmed your supervision assignment.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to confirm supervision assignment. ' . $e->getMessage());
        }
    }
    
    /**
     * Mark supervisor as attended.
     */
    public function markAttendance(Request $request, ExamSupervisor $supervisor)
    {
        $validatedData = $request->validate([
            'is_attended' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);
        
        try {
            $supervisor->update([
                'is_attended' => $validatedData['is_attended'],
                'notes' => $validatedData['notes'] ?? $supervisor->notes,
            ]);
            
            $status = $validatedData['is_attended'] ? 'attended' : 'absent';
            
            return redirect()->route('exam.supervisors.show', $supervisor)
                ->with('success', "Supervisor marked as {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update attendance status. ' . $e->getMessage());
        }
    }
    
    /**
     * List all supervision duties for the logged-in user.
     */
    public function myDuties(Request $request)
    {
        $query = ExamSupervisor::where('user_id', Auth::id())
            ->with(['schedule.exam', 'schedule.section']);
        
        // Filter by confirmation status
        if ($request->has('is_confirmed')) {
            $query->where('is_confirmed', $request->boolean('is_confirmed'));
        }
        
        // Filter by upcoming exams
        if ($request->has('upcoming')) {
            $query->whereHas('schedule', function ($q) {
                $q->whereDate('exam_date', '>=', Carbon::today());
            });
        }
        
        $duties = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('exam.supervisors.my_duties', compact('duties'));
    }
    
    /**
     * Bulk assign supervisors to multiple exam schedules.
     */
    public function bulkAssign(Request $request)
    {
        $validatedData = $request->validate([
            'schedules' => 'required|array',
            'schedules.*' => 'required|exists:exam_schedules,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:supervisor,invigilator,examiner,coordinator',
            'responsibilities' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $assignedCount = 0;
            $skippedCount = 0;
            
            foreach ($validatedData['schedules'] as $scheduleId) {
                $schedule = ExamSchedule::findOrFail($scheduleId);
                
                // Check if this supervisor is already assigned to this schedule
                $existingSupervisor = ExamSupervisor::where('exam_schedule_id', $scheduleId)
                    ->where('user_id', $validatedData['user_id'])
                    ->exists();
                    
                if ($existingSupervisor) {
                    $skippedCount++;
                    continue;
                }
                
                // Calculate reporting and leaving times based on exam start/end times
                $reportingTime = Carbon::parse($schedule->start_time)->subMinutes(30);
                $leavingTime = Carbon::parse($schedule->end_time)->addMinutes(30);
                
                ExamSupervisor::create([
                    'exam_schedule_id' => $scheduleId,
                    'user_id' => $validatedData['user_id'],
                    'role' => $validatedData['role'],
                    'responsibilities' => $validatedData['responsibilities'] ?? null,
                    'reporting_time' => $reportingTime,
                    'leaving_time' => $leavingTime,
                    'is_confirmed' => false,
                    'is_attended' => false,
                    'assigned_by' => Auth::id(),
                ]);
                
                $assignedCount++;
            }
            
            DB::commit();
            
            return redirect()->route('exam.supervisors.index')
                ->with('success', "Successfully assigned supervisor to {$assignedCount} exams. {$skippedCount} assignments were skipped (already assigned).");
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to assign supervisors. ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use App\Models\ExamSupervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamSupervisorController extends Controller
{
    /**
     * Display a listing of the supervisors.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ExamSupervisor::with(['schedule', 'schedule.exam', 'user', 'assignedBy']);
        
        // Apply filters if provided
        if ($request->filled('exam_schedule_id')) {
            $query->where('exam_schedule_id', $request->exam_schedule_id);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('is_confirmed')) {
            $query->where('is_confirmed', $request->is_confirmed === 'yes');
        }
        
        if ($request->filled('date')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('exam_date', $request->date);
            });
        }
        
        $supervisors = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get data for filters
        $schedules = ExamSchedule::all();
        $users = User::role(['Teacher', 'Admin'])->get();
        $roles = ExamSupervisor::getRoles();
        
        return view('exam_supervisors.index', compact(
            'supervisors',
            'schedules',
            'users',
            'roles'
        ));
    }

    /**
     * Store a newly created supervisor assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string',
            'reporting_time' => 'nullable',
            'leaving_time' => 'nullable',
            'responsibilities' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        // Set assigned_by to the current user
        $validated['assigned_by'] = Auth::id();
        $validated['is_confirmed'] = false;
        
        // Check if this user is already assigned to this schedule
        $existing = ExamSupervisor::where('exam_schedule_id', $validated['exam_schedule_id'])
            ->where('user_id', $validated['user_id'])
            ->first();
            
        if ($existing) {
            return redirect()
                ->back()
                ->with('error', 'This user is already assigned as a supervisor for this exam schedule.');
        }
        
        $supervisor = ExamSupervisor::create($validated);
        
        return redirect()
            ->back()
            ->with('success', 'Supervisor assigned successfully.');
    }

    /**
     * Display the specified supervisor.
     *
     * @param  \App\Models\ExamSupervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function show(ExamSupervisor $supervisor)
    {
        $supervisor->load(['schedule', 'schedule.exam', 'user', 'assignedBy']);
        
        return view('exam_supervisors.show', compact('supervisor'));
    }

    /**
     * Show the form for editing the specified supervisor.
     *
     * @param  \App\Models\ExamSupervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamSupervisor $supervisor)
    {
        $roles = ExamSupervisor::getRoles();
        $users = User::role(['Teacher', 'Admin'])->get();
        
        return view('exam_supervisors.edit', compact('supervisor', 'roles', 'users'));
    }

    /**
     * Update the specified supervisor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamSupervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExamSupervisor $supervisor)
    {
        $validated = $request->validate([
            'role' => 'required|string',
            'reporting_time' => 'nullable',
            'leaving_time' => 'nullable',
            'responsibilities' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_confirmed' => 'boolean',
            'is_attended' => 'boolean',
        ]);
        
        $supervisor->update($validated);
        
        return redirect()
            ->route('exam-supervisors.show', $supervisor)
            ->with('success', 'Supervisor updated successfully.');
    }

    /**
     * Remove the specified supervisor from storage.
     *
     * @param  \App\Models\ExamSupervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExamSupervisor $supervisor)
    {
        $supervisor->delete();
        
        return redirect()
            ->back()
            ->with('success', 'Supervisor removed successfully.');
    }
    
    /**
     * Confirm supervision duty.
     *
     * @param  \App\Models\ExamSupervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function confirm(ExamSupervisor $supervisor)
    {
        // Only allow users to confirm their own supervisions
        if (Auth::id() != $supervisor->user_id && !Auth::user()->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->back()->with('error', 'You do not have permission to confirm this supervision.');
        }
        
        $supervisor->confirm();
        
        return redirect()
            ->back()
            ->with('success', 'Supervision confirmed successfully.');
    }
    
    /**
     * Mark supervision as attended.
     *
     * @param  \App\Models\ExamSupervisor  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function markAttended(ExamSupervisor $supervisor)
    {
        // Only admins can mark attendance
        if (!Auth::user()->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->back()->with('error', 'You do not have permission to mark attendance.');
        }
        
        $supervisor->markAttended();
        
        return redirect()
            ->back()
            ->with('success', 'Supervisor marked as attended.');
    }
    
    /**
     * Display assigned supervisions for the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function mySupervisionsAssignments()
    {
        $supervisions = ExamSupervisor::with(['schedule', 'schedule.exam'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('exam_supervisors.my_supervisions', compact('supervisions'));
    }
} 
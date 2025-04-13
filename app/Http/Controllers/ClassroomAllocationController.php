<?php

namespace App\Http\Controllers;

use App\Models\ClassroomAllocation;
use App\Models\Section;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class ClassroomAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view classroom allocations'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:create classroom allocations'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit classroom allocations'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete classroom allocations'])->only(['destroy']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allocations = ClassroomAllocation::with(['section.class', 'academicSession'])
            ->paginate(10);
        return view('classroom-allocations.index', compact('allocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Section::with('class')->where('status', 'active')->get();
        $academicSessions = AcademicSession::all();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $types = ['lecture', 'lab', 'seminar', 'other'];
        
        return view('classroom-allocations.create', compact('sections', 'academicSessions', 'days', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|max:255',
            'floor' => 'nullable|integer',
            'building' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:lecture,lab,seminar,other',
            'status' => 'required|in:available,maintenance,reserved',
            'section_id' => 'required|exists:sections,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
        ]);

        // Check if room is already allocated for this time slot
        $conflictingAllocations = ClassroomAllocation::where('room_number', $request->room_number)
            ->where('day', $request->day)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })->count();

        if ($conflictingAllocations > 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Room is already allocated for this time slot. Please choose another time or room.');
        }

        ClassroomAllocation::create($request->all());

        return redirect()->route('classroom-allocations.index')
            ->with('success', 'Classroom allocation created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassroomAllocation $classroomAllocation)
    {
        $classroomAllocation->load(['section.class', 'section.teacher', 'academicSession']);
        
        return view('classroom-allocations.show', compact('classroomAllocation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassroomAllocation $classroomAllocation)
    {
        $sections = Section::with('class')->where('status', 'active')->get();
        $academicSessions = AcademicSession::all();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $types = ['lecture', 'lab', 'seminar', 'other'];
        
        return view('classroom-allocations.edit', compact('classroomAllocation', 'sections', 'academicSessions', 'days', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassroomAllocation $classroomAllocation)
    {
        $request->validate([
            'room_number' => 'required|string|max:255',
            'floor' => 'nullable|integer',
            'building' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:lecture,lab,seminar,other',
            'status' => 'required|in:available,maintenance,reserved',
            'section_id' => 'required|exists:sections,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
        ]);

        // Check if room is already allocated for this time slot (except for this allocation)
        $conflictingAllocations = ClassroomAllocation::where('room_number', $request->room_number)
            ->where('day', $request->day)
            ->where('id', '!=', $classroomAllocation->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })->count();

        if ($conflictingAllocations > 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Room is already allocated for this time slot. Please choose another time or room.');
        }

        $classroomAllocation->update($request->all());

        return redirect()->route('classroom-allocations.index')
            ->with('success', 'Classroom allocation updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassroomAllocation $classroomAllocation)
    {
        $classroomAllocation->delete();

        return redirect()->route('classroom-allocations.index')
            ->with('success', 'Classroom allocation deleted successfully');
    }
}

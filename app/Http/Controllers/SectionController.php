<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view sections'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:create sections'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit sections'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete sections'])->only(['destroy']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::with(['class', 'teacher'])
            ->paginate(10);
        return view('sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = Classes::where('status', 'active')->get();
        $teachers = User::role('teacher')->get();
        
        return view('sections.create', compact('classes', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'section_name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'capacity' => 'nullable|integer|min:1',
            'teacher_id' => 'nullable|exists:users,id',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Section::create($request->all());

        return redirect()->route('sections.index')
            ->with('success', 'Section created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        $section->load(['class', 'teacher', 'classroomAllocations']);
        
        // Get all students from the class
        $students = $section->class->students;
        
        return view('sections.show', compact('section', 'students'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        $classes = Classes::where('status', 'active')->get();
        $teachers = User::role('teacher')->get();
        
        return view('sections.edit', compact('section', 'classes', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section)
    {
        $request->validate([
            'section_name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'capacity' => 'nullable|integer|min:1',
            'teacher_id' => 'nullable|exists:users,id',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $section->update($request->all());

        return redirect()->route('sections.index')
            ->with('success', 'Section updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        // Check if section has attendance records
        if ($section->attendances()->count() > 0) {
            return redirect()->route('sections.index')
                ->with('error', 'Cannot delete section with attendance records. Please remove attendance records first.');
        }
        
        // Check if section has classroom allocations
        if ($section->classroomAllocations()->count() > 0) {
            return redirect()->route('sections.index')
                ->with('error', 'Cannot delete section with classroom allocations. Please remove allocations first.');
        }

        $section->delete();

        return redirect()->route('sections.index')
            ->with('success', 'Section deleted successfully');
    }
}

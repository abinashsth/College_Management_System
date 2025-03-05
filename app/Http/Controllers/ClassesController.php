<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classes::with(['course', 'session', 'faculty'])->orderBy('id', 'desc')->paginate(10);
        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::where('status', 'active')->get();
        $sessions = Session::where('status', 'active')->get();
        $faculties = Faculty::all();
        
        return view('classes.create', compact('courses', 'sessions', 'faculties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'faculty_id' => 'required|exists:faculties,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Classes::create([
            'class_name' => $request->class_name,
            'course_id' => $request->course_id,
            'session_id' => $request->session_id,
            'faculty_id' => $request->faculty_id,
            'status' => $request->status,
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classes $class)
    {
        $class->load(['course', 'session', 'faculty', 'students', 'subjects']);
        return view('classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classes $class)
    {
        $courses = Course::where('status', 'active')->get();
        $sessions = Session::where('status', 'active')->get();
        $faculties = Faculty::all();
        
        return view('classes.edit', compact('class', 'courses', 'sessions', 'faculties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classes $class)
    {
        $validator = Validator::make($request->all(), [
            'class_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'faculty_id' => 'required|exists:faculties,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $class->update([
            'class_name' => $request->class_name,
            'course_id' => $request->course_id,
            'session_id' => $request->session_id,
            'faculty_id' => $request->faculty_id,
            'status' => $request->status,
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classes $class)
    {
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classes;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view students')->only(['index', 'show']);
        $this->middleware('permission:create students')->only(['create', 'store']);
        $this->middleware('permission:edit students')->only(['edit', 'update']);
        $this->middleware('permission:delete students')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Student::with(['class', 'session']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('student_name', 'like', "%{$search}%")
                      ->orWhere('father_name', 'like', "%{$search}%")
                      ->orWhere('roll_no', 'like', "%{$search}%")
                      ->orWhere('admission_number', 'like', "%{$search}%");
                });
            }

            // Class filter
            if ($request->filled('class')) {
                $query->where('class_id', $request->class);
            }

            // Session filter
            if ($request->filled('session')) {
                $query->where('session_id', $request->session);
            }

            $students = $query->orderBy('student_name')->paginate(10);
            $classes = Classes::where('status', 'active')->get();
            $sessions = Session::where('status', true)->get();

            return view('students.index', compact('students', 'classes', 'sessions'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error loading students: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $classes = Classes::where('status', 'active')->get();
            $sessions = Session::where('status', true)->get();

            if ($classes->isEmpty()) {
                return redirect()->route('classes.create')
                    ->with('error', 'Please create at least one active class first.');
            }

            if ($sessions->isEmpty()) {
                return redirect()->route('sessions.create')
                    ->with('error', 'Please create at least one active session first.');
            }

            return view('students.create', compact('classes', 'sessions'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'student_name' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'mother_name' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'address' => 'required|string',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255|unique:students',
                'admission_number' => 'nullable|string|max:50|unique:students',
                'roll_no' => 'required|string|max:50|unique:students',
                'admission_date' => 'required|date',
                'class_id' => 'required|exists:classes,id',
                'session_id' => 'required|exists:academic_sessions,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Get class details to set course_id and faculty_id
            $class = Classes::with(['course', 'faculty'])->find($request->class_id);
            
            if (!$class) {
                return redirect()->back()
                    ->with('error', 'Selected class not found or is invalid.')
                    ->withInput();
            }
            
            // Prepare student data
            $studentData = $request->all();
            
            // Set course_id and faculty_id based on the selected class
            $studentData['course_id'] = $class->course_id;
            $studentData['faculty_id'] = $class->faculty_id;
            
            // Set created_by
            $studentData['created_by'] = Auth::id();

            // Create the student
            $student = Student::create($studentData);

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            // Log the detailed error
            \Log::error('Student creation failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Error creating student: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        try {
            $student->load(['class', 'session']);
            return view('students.show', compact('student'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error viewing student: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        try {
            $classes = Classes::where('status', 'active')->get();
            $sessions = Session::where('status', true)->get();
            return view('students.edit', compact('student', 'classes', 'sessions'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        try {
            $validator = Validator::make($request->all(), [
                'student_name' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'mother_name' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'address' => 'required|string',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255|unique:students,email,' . $student->id,
                'admission_number' => 'required|string|max:50|unique:students,admission_number,' . $student->id,
                'roll_no' => 'required|string|max:50|unique:students,roll_no,' . $student->id,
                'admission_date' => 'required|date',
                'class_id' => 'required|exists:classes,id',
                'session_id' => 'required|exists:academic_sessions,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Get class details to set course_id and faculty_id
            $class = Classes::with(['course', 'faculty'])->find($request->class_id);
            
            if ($class) {
                // Set course_id and faculty_id based on the selected class
                $request->merge([
                    'course_id' => $class->course_id,
                    'faculty_id' => $class->faculty_id
                ]);
            }
            
            // Update the student with all data including admission_number
            $student->update($request->all());

            return redirect()->route('students.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating student: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        try {
            $student->delete();
            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }
} 
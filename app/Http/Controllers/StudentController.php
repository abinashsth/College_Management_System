<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view students']);
    }

    public function index()
    {
        $students = Student::with('class')->paginate(10);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        $classes = Classes::all();
        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'class_id' => 'required|exists:classes,id',
        ]);

        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'dob' => $request->dob,
            'class_id' => $request->class_id,
            'status' => true,
        ]);

        // Create a user account for the student
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole('student');

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully');
    }

    public function edit(Student $student)
    {
        $classes = Classes::all();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email,' . $student->id,
            'address' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'class_id' => 'required|exists:classes,id',
            'status' => 'boolean'
        ]);

        $student->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'dob' => $request->dob,
            'class_id' => $request->class_id,
            'status' => $request->status ?? false,
        ]);

        // Update the corresponding user account
        if ($user = User::where('email', $student->getOriginal('email'))->first()) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        // Delete the corresponding user account
        if ($user = User::where('email', $student->email)->first()) {
            $user->delete();
        }

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully');
    }
}

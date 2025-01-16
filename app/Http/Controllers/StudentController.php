<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
   
    public function index()
    {
        $students = Student::with('class')->paginate(10);
        return view('students.index', compact('students'));
    }
    public function create()
    {
        $classes = ClassModel::all();
        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'dob' => 'required|date',
            'email' => 'nullable|email|unique:students',
            'password' => 'required|min:6',
            'class_id' => 'required|exists:classes,id',
            'status' => 'required|boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully');
    }

    public function edit(Student $student)
    {
        $classes = ClassModel::all();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'dob' => 'required|date',
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'class_id' => 'required|exists:classes,id',
            'status' => 'required|boolean'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully');
    }
}
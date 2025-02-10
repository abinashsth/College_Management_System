<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentAdmissionController extends Controller
{
    public function create()
    {
        return view('students.admit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'telephone' => 'nullable|string|max:20',
            'gender' => 'required|string|in:male,female,other',
            'date_of_birth' => 'required|date',
            'nationality' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'lga' => 'required|string|max:100',
            'blood_group' => 'required|string|max:10',
            'passport_photo' => 'required|image|mimes:jpeg,png|max:2048'
        ]);

        $photoPath = $request->file('passport_photo')->store('passport_photos', 'public');

        $student = Student::create([
            'name' => $request->full_name,
            'email' => $request->email,
            'address' => $request->address,
            'contact_number' => $request->phone,
            'telephone' => $request->telephone,
            'gender' => $request->gender,
            'dob' => $request->date_of_birth,
            'nationality' => $request->nationality,
            'state' => $request->state,
            'lga' => $request->lga,
            'blood_group' => $request->blood_group,
            'passport_photo' => $photoPath,
            'password' => Hash::make('password123'), // Default password
            'status' => true
        ]);

        return redirect()->route('students.index')->with('success', 'Student admitted successfully!');
    }
}

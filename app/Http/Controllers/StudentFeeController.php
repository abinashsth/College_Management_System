<?php

namespace App\Http\Controllers;

use App\Models\StudentFee;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentFeeController extends Controller
{
    public function index()
    {
        $studentFees = StudentFee::with('student')->get();
        return view('student_fee.index', compact('studentFees'));
    }

    public function create()
    {
        $students = Student::all();
        return view('student_fee.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:Paid,Unpaid'
        ]);

        StudentFee::create($validated);
        return redirect()->route('student-fee.index')->with('success', 'Fee record created successfully');
    }

    public function show(StudentFee $studentFee)
    {
        return view('student_fee.show', compact('studentFee'));
    }

    public function edit(StudentFee $studentFee)
    {
        $students = Student::all();
        return view('student_fee.edit', compact('studentFee', 'students'));
    }

    public function update(Request $request, StudentFee $studentFee)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:Paid,Unpaid'
        ]);

        $studentFee->update($validated);
        return redirect()->route('student-fee.index')->with('success', 'Fee record updated successfully');
    }

    public function destroy(StudentFee $studentFee)
    {
        $studentFee->delete();
        return redirect()->route('student-fee.index')->with('success', 'Fee record deleted successfully');
    }
}
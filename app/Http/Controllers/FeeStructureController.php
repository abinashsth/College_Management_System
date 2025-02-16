<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeStructure;
use App\Models\Classes;
use App\Models\AcademicYear;
use App\Models\Student;


class FeeStructureController extends Controller
{
    public function index()
    {
        $feeStructures = FeeStructure::with(['student', 'class', 'academicYear'])->paginate(10);
        $classes = Classes::all();
        $academicYears = AcademicYear::all();
        $students = Student::all();
        return view('account.fee_management.fee_structure.index', compact('feeStructures', 'classes', 'academicYears', 'students'));
    }

    public function create()
    {
        $classes = Classes::all();
        $academicYears = AcademicYear::where('status', 'active')->get();
        $students = Student::where('status', true)->get();
        return view('account.fee_management.fee_structure.create', compact('classes', 'academicYears', 'students'));
    }   

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year' => 'required|exists:academic_years,id',
            'tuition_fee' => 'required|numeric|min:0',
            'admission_fee' => 'required|numeric|min:0',
            'exam_fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $feeStructure = FeeStructure::create($validated);
        
        return redirect()
            ->route('account.fee_management.fee_structure.index')
            ->with('success', 'Fee structure created successfully');
    }   

    public function edit($id)
    {
        $feeStructure = FeeStructure::findOrFail($id);
        $classes = Classes::all();
        $academicYears = AcademicYear::where('status', 'active')->get();
        $students = Student::where('status', true)->get();
        return view('account.fee_management.fee_structure.edit', compact('feeStructure', 'classes', 'academicYears', 'students'));
    }   

    public function update(Request $request, $id)
    {
        $feeStructure = FeeStructure::findOrFail($id);
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id', 
            'academic_year' => 'required|exists:academic_years,id',
            'tuition_fee' => 'required|numeric|min:0',
            'admission_fee' => 'required|numeric|min:0',
            'exam_fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $feeStructure->update($validated);
        
        return redirect()
            ->route('account.fee_management.fee_structure.index')
            ->with('success', 'Fee structure updated successfully');
    }      

    public function destroy($id)
    {
        $feeStructure = FeeStructure::findOrFail($id);
        $feeStructure->delete();
        
        return redirect()
            ->route('account.fee_management.fee_structure.index')
            ->with('success', 'Fee structure deleted successfully');
    }
}

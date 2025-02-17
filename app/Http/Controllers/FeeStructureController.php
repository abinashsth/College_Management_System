<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeStructure;
use App\Models\Classes;
use App\Models\Student;
use App\Models\FeeCategory;

class FeeStructureController extends Controller
{
    public function index()
    {
        $feeStructures = FeeStructure::with(['student', 'class'])->paginate(10);
        $classes = Classes::all();
        $students = Student::all();
        $feeCategories = FeeCategory::all();
        return view('account.fee_management.fee_structure.index', compact('feeStructures', 'classes', 'students', 'feeCategories'));
    }

    public function create()
    {
        $classes = Classes::all();
        $students = Student::where('status', true)->get();
        $feeCategories = FeeCategory::all();
        return view('account.fee_management.fee_structure.create', compact('classes', 'students', 'feeCategories'));
    }   

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
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
        $students = Student::where('status', true)->get();
        return view('account.fee_management.fee_structure.edit', compact('feeStructure', 'classes', 'students'));
    }   

    public function update(Request $request, $id)
    {
        $feeStructure = FeeStructure::findOrFail($id);
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id', 
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

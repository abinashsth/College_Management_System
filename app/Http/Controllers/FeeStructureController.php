<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FeeStructure;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index()
    {
        $feeStructures = FeeStructure::with('course')->paginate(10);
        return view('fee-structures.index', compact('feeStructures'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('fee-structures.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1',
            'tuition_fee' => 'required|numeric|min:0',
            'development_fee' => 'required|numeric|min:0',
            'other_charges' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $validated['total_amount'] = 
            $validated['tuition_fee'] + 
            $validated['development_fee'] + 
            $validated['other_charges'];

        FeeStructure::create($validated);

        return redirect()->route('fee-structures.index')
            ->with('success', 'Fee structure created successfully');
    }

    public function edit(FeeStructure $feestructure)
    {
        $courses = Course::all();
        return view('fee-structures.edit', compact('feestructure', 'courses'));
    }

    public function update(Request $request, FeeStructure $feestructure)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1',
            'tuition_fee' => 'required|numeric|min:0',
            'development_fee' => 'required|numeric|min:0',
            'other_charges' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $validated['total_amount'] = 
            $validated['tuition_fee'] + 
            $validated['development_fee'] + 
            $validated['other_charges'];

        $feestructure->update($validated);

        return redirect()->route('fee-structures.index')
            ->with('success', 'Fee structure updated successfully');
    }

    public function destroy(FeeStructure $feestructure)
    {
        $feestructure->delete();
        return redirect()->route('fee-structures.index')
            ->with('success', 'Fee structure deleted successfully');
    }
}
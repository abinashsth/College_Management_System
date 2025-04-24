<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\Program;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index()
    {
        $feeStructures = FeeStructure::with(['program', 'academicSession'])->latest()->paginate(10);
        return view('fee-structures.index', compact('feeStructures'));
    }

    public function create()
    {
        $programs = Program::all();
        $academicSessions = AcademicSession::all();
        return view('fee-structures.create', compact('programs', 'academicSessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'fee_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_day' => 'required|integer|min:1|max:31',
            'description' => 'nullable|string'
        ]);

        FeeStructure::create($validated);
        return redirect()->route('fee-structures.index')->with('success', 'Fee structure created successfully');
    }

    public function show(FeeStructure $feeStructure)
    {
        $feeStructure->load(['program', 'academicSession']);
        return view('fee-structures.show', compact('feeStructure'));
    }

    public function edit(FeeStructure $feeStructure)
    {
        $programs = Program::all();
        $academicSessions = AcademicSession::all();
        return view('fee-structures.edit', compact('feeStructure', 'programs', 'academicSessions'));
    }

    public function update(Request $request, FeeStructure $feeStructure)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'fee_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_day' => 'required|integer|min:1|max:31',
            'description' => 'nullable|string'
        ]);

        $feeStructure->update($validated);
        return redirect()->route('fee-structures.index')->with('success', 'Fee structure updated successfully');
    }

    public function destroy(FeeStructure $feeStructure)
    {
        $feeStructure->delete();
        return redirect()->route('fee-structures.index')->with('success', 'Fee structure deleted successfully');
    }

    public function getFeeStructure(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'academic_session_id' => 'required|exists:academic_sessions,id'
        ]);

        $feeStructures = FeeStructure::where('program_id', $validated['program_id'])
            ->where('academic_session_id', $validated['academic_session_id'])
            ->get();

        return response()->json($feeStructures);
    }
}
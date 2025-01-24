<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        return view('academic-sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('academic-sessions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:academic_sessions',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        if ($request->is_active) {
            // Deactivate all other sessions
            AcademicSession::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicSession::create($request->all());

        return redirect()->route('academic-sessions.index')
            ->with('success', 'Academic session created successfully.');
    }

    public function edit(AcademicSession $academicSession)
    {
        return view('academic-sessions.edit', compact('academicSession'));
    }

    public function update(Request $request, AcademicSession $academicSession)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:academic_sessions,name,' . $academicSession->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        if ($request->is_active) {
            // Deactivate all other sessions
            AcademicSession::where('id', '!=', $academicSession->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $academicSession->update($request->all());

        return redirect()->route('academic-sessions.index')
            ->with('success', 'Academic session updated successfully.');
    }

    public function destroy(AcademicSession $academicSession)
    {
        if ($academicSession->exams()->exists()) {
            return back()->with('error', 'Cannot delete this session as it has associated exams.');
        }

        $academicSession->delete();

        return redirect()->route('academic-sessions.index')
            ->with('success', 'Academic session deleted successfully.');
    }
}

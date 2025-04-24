<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicYears = AcademicYear::with('sessions')->orderBy('start_date', 'desc')->get();
        return view('settings.academic-year.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.academic-year.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
            'description' => 'nullable|string',
        ]);

        // Format dates for consistent storage
        if (isset($validated['start_date'])) {
            $validated['start_date'] = \Carbon\Carbon::parse($validated['start_date'])->format('Y-m-d');
        }
        
        if (isset($validated['end_date'])) {
            $validated['end_date'] = \Carbon\Carbon::parse($validated['end_date'])->format('Y-m-d');
        }

        // If this is set as current, unset any other current academic year
        if ($request->has('is_current') && $request->is_current) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        AcademicYear::create($validated);

        return redirect()->route('settings.academic-year.index')
            ->with('success', 'Academic year created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        $academicYear->load('sessions');
        return view('settings.academic-year.show', compact('academicYear'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('settings.academic-year.edit', compact('academicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
            'description' => 'nullable|string',
        ]);

        // Format dates to ensure consistent storage
        if (isset($validated['start_date'])) {
            $validated['start_date'] = \Carbon\Carbon::parse($validated['start_date'])->format('Y-m-d');
        }
        
        if (isset($validated['end_date'])) {
            $validated['end_date'] = \Carbon\Carbon::parse($validated['end_date'])->format('Y-m-d');
        }

        // If this is set as current, unset any other current academic year
        if ($request->has('is_current') && $request->is_current && !$academicYear->is_current) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        $academicYear->update($validated);

        return redirect()->route('settings.academic-year.index')
            ->with('success', 'Academic year updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Check if the academic year has sessions
        if ($academicYear->sessions()->count() > 0) {
            return back()->with('error', 'Cannot delete an academic year with sessions');
        }

        $academicYear->delete();

        return redirect()->route('settings.academic-year.index')
            ->with('success', 'Academic year deleted successfully');
    }

    /**
     * Show the form for creating a new session.
     */
    public function createSession(AcademicYear $academicYear)
    {
        return view('settings.academic-year.create-session', compact('academicYear'));
    }

    /**
     * Store a newly created session in storage.
     */
    public function storeSession(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:semester,term,quarter',
            'start_date' => 'required|date|after_or_equal:' . $academicYear->start_date . '|before_or_equal:' . $academicYear->end_date,
            'end_date' => 'required|date|after:start_date|before_or_equal:' . $academicYear->end_date,
            'is_current' => 'boolean',
            'description' => 'nullable|string',
            'registration_start_date' => 'nullable|date|after_or_equal:' . $academicYear->start_date . '|before:end_date',
            'registration_end_date' => 'nullable|date|after_or_equal:registration_start_date|before:end_date',
            'class_start_date' => 'nullable|date|after_or_equal:' . $academicYear->start_date . '|before:end_date',
            'class_end_date' => 'nullable|date|after_or_equal:class_start_date|before:end_date',
            'exam_start_date' => 'nullable|date|after_or_equal:class_start_date|before:end_date',
            'exam_end_date' => 'nullable|date|after_or_equal:exam_start_date|before:end_date',
            'result_date' => 'nullable|date|after_or_equal:exam_start_date|before_or_equal:' . $academicYear->end_date,
        ]);

        // Format all date fields for consistent storage
        $dateFields = [
            'start_date', 'end_date', 'registration_start_date', 'registration_end_date',
            'class_start_date', 'class_end_date', 'exam_start_date', 'exam_end_date', 
            'result_date'
        ];
        
        foreach ($dateFields as $dateField) {
            if (isset($validated[$dateField])) {
                $validated[$dateField] = \Carbon\Carbon::parse($validated[$dateField])->format('Y-m-d');
            }
        }

        // Check if the name is unique for this academic year
        if (AcademicSession::where('academic_year_id', $academicYear->id)
            ->where('name', $request->name)
            ->exists()) {
            return back()->with('error', 'A session with this name already exists for this academic year')->withInput();
        }

        // If this is set as current, unset any other current session
        if ($request->has('is_current') && $request->is_current) {
            AcademicSession::where('is_current', true)->update(['is_current' => false]);
        }

        $validated['academic_year_id'] = $academicYear->id;
        AcademicSession::create($validated);

        return redirect()->route('settings.academic-year.show', $academicYear)
            ->with('success', 'Academic session created successfully');
    }

    /**
     * Display the specified session.
     */
    public function showSession(AcademicYear $academicYear, AcademicSession $session)
    {
        $session->load('academicYear');
        return view('settings.academic-year.show-session', compact('academicYear', 'session'));
    }

    /**
     * Set the current session.
     */
    public function setCurrentSession(AcademicYear $academicYear, AcademicSession $session)
    {
        // Clear current status from all sessions
        AcademicSession::where('is_current', true)->update(['is_current' => false]);
        
        // Set this session as current
        $session->update(['is_current' => true]);
        
        // Also set the associated academic year as current
        AcademicYear::where('is_current', true)->update(['is_current' => false]);
        $academicYear->update(['is_current' => true]);
        
        return redirect()->route('settings.academic-year.sessions.show', [$academicYear, $session])
            ->with('success', 'Session has been set as the current active session.');
    }

    /**
     * Show the form for editing a session.
     */
    public function editSession(AcademicYear $academicYear, AcademicSession $session)
    {
        return view('settings.academic-year.edit-session', compact('academicYear', 'session'));
    }

    /**
     * Update the specified session in storage.
     */
    public function updateSession(Request $request, AcademicYear $academicYear, AcademicSession $session)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:semester,term,quarter',
            'start_date' => 'required|date|after_or_equal:' . $academicYear->start_date . '|before_or_equal:' . $academicYear->end_date,
            'end_date' => 'required|date|after:start_date|before_or_equal:' . $academicYear->end_date,
            'is_current' => 'boolean',
            'description' => 'nullable|string',
            'registration_start_date' => 'nullable|date|after_or_equal:' . $academicYear->start_date . '|before:end_date',
            'registration_end_date' => 'nullable|date|after_or_equal:registration_start_date|before:end_date',
            'class_start_date' => 'nullable|date|after_or_equal:' . $academicYear->start_date . '|before:end_date',
            'class_end_date' => 'nullable|date|after_or_equal:class_start_date|before:end_date',
            'exam_start_date' => 'nullable|date|after_or_equal:class_start_date|before:end_date',
            'exam_end_date' => 'nullable|date|after_or_equal:exam_start_date|before:end_date',
            'result_date' => 'nullable|date|after_or_equal:exam_start_date|before_or_equal:' . $academicYear->end_date,
        ]);

        // Format all date fields for consistent storage
        $dateFields = [
            'start_date', 'end_date', 'registration_start_date', 'registration_end_date',
            'class_start_date', 'class_end_date', 'exam_start_date', 'exam_end_date', 
            'result_date'
        ];
        
        foreach ($dateFields as $dateField) {
            if (isset($validated[$dateField])) {
                $validated[$dateField] = \Carbon\Carbon::parse($validated[$dateField])->format('Y-m-d');
            }
        }

        // Check if the name is unique for this academic year
        if (AcademicSession::where('academic_year_id', $academicYear->id)
            ->where('name', $request->name)
            ->where('id', '!=', $session->id)
            ->exists()) {
            return back()->with('error', 'A session with this name already exists for this academic year')->withInput();
        }

        // If this is set as current, unset any other current session
        if ($request->has('is_current') && $request->is_current && !$session->is_current) {
            AcademicSession::where('is_current', true)->update(['is_current' => false]);
        }

        $session->update($validated);

        return redirect()->route('settings.academic-year.show', $academicYear)
            ->with('success', 'Academic session updated successfully');
    }

    /**
     * Remove the specified session from storage.
     */
    public function destroySession(AcademicYear $academicYear, AcademicSession $session)
    {
        // Check if there are any dependencies (classes, exams, etc.)
        // This would be implemented based on your actual relationships
        
        $session->delete();

        return redirect()->route('settings.academic-year.show', $academicYear)
            ->with('success', 'Academic session deleted successfully');
    }
}

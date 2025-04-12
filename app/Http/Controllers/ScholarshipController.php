<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\Student;
use App\Models\StudentScholarship;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
    /**
     * Display a listing of the scholarships.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scholarships = Scholarship::withCount('students')->orderBy('name')->get();
        return view('finance.scholarships.index', compact('scholarships'));
    }

    /**
     * Show the form for creating a new scholarship.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = [
            'full' => 'Full Scholarship (100%)',
            'partial' => 'Partial Scholarship',
            'merit' => 'Merit-based',
            'need' => 'Need-based',
            'sports' => 'Sports Scholarship',
            'cultural' => 'Cultural Scholarship',
            'other' => 'Other'
        ];
        
        return view('finance.scholarships.create', compact('types'));
    }

    /**
     * Store a newly created scholarship in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:scholarships',
            'description' => 'nullable|string',
            'type' => 'required|in:full,partial,merit,need,sports,cultural,other',
            'amount' => 'required_if:type,partial|nullable|numeric|min:0',
            'amount_type' => 'required|in:percentage,fixed',
            'is_active' => 'boolean',
            'criteria' => 'nullable|string',
            'sponsor' => 'nullable|string|max:255',
        ]);
        
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');
        
        $scholarship = Scholarship::create($validated);
        
        return redirect()->route('scholarships.index')
            ->with('success', 'Scholarship created successfully.');
    }

    /**
     * Display the specified scholarship.
     *
     * @param  \App\Models\Scholarship  $scholarship
     * @return \Illuminate\Http\Response
     */
    public function show(Scholarship $scholarship)
    {
        $scholarship->load(['students.student', 'students.academicYear']);
        return view('finance.scholarships.show', compact('scholarship'));
    }

    /**
     * Show the form for editing the specified scholarship.
     *
     * @param  \App\Models\Scholarship  $scholarship
     * @return \Illuminate\Http\Response
     */
    public function edit(Scholarship $scholarship)
    {
        $types = [
            'full' => 'Full Scholarship (100%)',
            'partial' => 'Partial Scholarship',
            'merit' => 'Merit-based',
            'need' => 'Need-based',
            'sports' => 'Sports Scholarship',
            'cultural' => 'Cultural Scholarship',
            'other' => 'Other'
        ];
        
        return view('finance.scholarships.edit', compact('scholarship', 'types'));
    }

    /**
     * Update the specified scholarship in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Scholarship  $scholarship
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Scholarship $scholarship)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:scholarships,code,' . $scholarship->id,
            'description' => 'nullable|string',
            'type' => 'required|in:full,partial,merit,need,sports,cultural,other',
            'amount' => 'required_if:type,partial|nullable|numeric|min:0',
            'amount_type' => 'required|in:percentage,fixed',
            'is_active' => 'boolean',
            'criteria' => 'nullable|string',
            'sponsor' => 'nullable|string|max:255',
        ]);
        
        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');
        
        $scholarship->update($validated);
        
        return redirect()->route('scholarships.index')
            ->with('success', 'Scholarship updated successfully.');
    }

    /**
     * Remove the specified scholarship from storage.
     *
     * @param  \App\Models\Scholarship  $scholarship
     * @return \Illuminate\Http\Response
     */
    public function destroy(Scholarship $scholarship)
    {
        // Check if the scholarship has active student assignments
        if ($scholarship->students()->count() > 0) {
            return redirect()->route('scholarships.index')
                ->with('error', 'Cannot delete a scholarship that has associated students.');
        }
        
        $scholarship->delete();
        
        return redirect()->route('scholarships.index')
            ->with('success', 'Scholarship deleted successfully.');
    }
    
    /**
     * Show form to assign scholarship to students.
     *
     * @param  \App\Models\Scholarship  $scholarship
     * @return \Illuminate\Http\Response
     */
    public function assignForm(Scholarship $scholarship)
    {
        $students = Student::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        
        return view('finance.scholarships.assign', compact('scholarship', 'students', 'academicYears'));
    }
    
    /**
     * Assign scholarship to students.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Scholarship  $scholarship
     * @return \Illuminate\Http\Response
     */
    public function assign(Request $request, Scholarship $scholarship)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'required_if:scholarship_type,partial|nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            foreach ($validated['student_ids'] as $studentId) {
                // Check if student already has this scholarship for this academic year
                $exists = StudentScholarship::where('student_id', $studentId)
                                         ->where('scholarship_id', $scholarship->id)
                                         ->where('academic_year_id', $validated['academic_year_id'])
                                         ->exists();
                
                if (!$exists) {
                    StudentScholarship::create([
                        'student_id' => $studentId,
                        'scholarship_id' => $scholarship->id,
                        'academic_year_id' => $validated['academic_year_id'],
                        'amount' => $validated['amount'] ?? $scholarship->amount,
                        'start_date' => $validated['start_date'],
                        'end_date' => $validated['end_date'],
                        'notes' => $validated['notes'],
                        'created_by' => Auth::id(),
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('scholarships.show', $scholarship)
                ->with('success', 'Scholarship assigned to students successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to assign scholarship: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Remove scholarship assignment from a student.
     *
     * @param  \App\Models\StudentScholarship  $studentScholarship
     * @return \Illuminate\Http\Response
     */
    public function removeAssignment(StudentScholarship $studentScholarship)
    {
        $scholarship = $studentScholarship->scholarship;
        
        $studentScholarship->delete();
        
        return redirect()->route('scholarships.show', $scholarship)
            ->with('success', 'Scholarship assignment removed successfully.');
    }
} 
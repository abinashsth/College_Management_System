<?php

namespace App\Http\Controllers;

use App\Models\FeeAllocation;
use App\Models\FeeType;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeAllocationController extends Controller
{
    /**
     * Display a listing of fee allocations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $feeAllocations = FeeAllocation::with(['feeType', 'academicYear'])
                                      ->orderBy('created_at', 'desc')
                                      ->paginate(15);
        
        return view('finance.fee-allocations.index', compact('feeAllocations'));
    }

    /**
     * Show the form for creating a new fee allocation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $feeTypes = FeeType::where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $programs = Program::orderBy('name')->get();
        $classes = Classes::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        
        $applicableTypes = [
            'program' => 'Program',
            'class' => 'Class',
            'section' => 'Section',
            'student' => 'Individual Student',
        ];
        
        return view('finance.fee-allocations.create', compact(
            'feeTypes',
            'academicYears',
            'programs',
            'classes',
            'sections',
            'applicableTypes'
        ));
    }

    /**
     * Store a newly created fee allocation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fee_type_id' => 'required|exists:fee_types,id',
            'applicable_to' => 'required|in:class,program,section,student',
            'academic_year_id' => 'required|exists:academic_years,id',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'academic_term' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        
        // Validate applicable_id based on applicable_to
        if ($request->applicable_to === 'program') {
            $request->validate(['program_id' => 'required|exists:programs,id']);
            $validated['applicable_id'] = $request->program_id;
        } elseif ($request->applicable_to === 'class') {
            $request->validate(['class_id' => 'required|exists:classes,id']);
            $validated['applicable_id'] = $request->class_id;
        } elseif ($request->applicable_to === 'section') {
            $request->validate(['section_id' => 'required|exists:sections,id']);
            $validated['applicable_id'] = $request->section_id;
        } elseif ($request->applicable_to === 'student') {
            $request->validate(['student_id' => 'required|exists:students,id']);
            $validated['applicable_id'] = $request->student_id;
        }
        
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');
        
        $feeAllocation = FeeAllocation::create($validated);
        
        return redirect()->route('fee-allocations.index')
            ->with('success', 'Fee allocation created successfully.');
    }

    /**
     * Display the specified fee allocation.
     *
     * @param  \App\Models\FeeAllocation  $feeAllocation
     * @return \Illuminate\Http\Response
     */
    public function show(FeeAllocation $feeAllocation)
    {
        $feeAllocation->load('feeType', 'academicYear');
        
        // Load the applicable entity based on applicable_to
        $applicableEntity = null;
        if ($feeAllocation->applicable_to === 'program' && $feeAllocation->applicable_id) {
            $applicableEntity = Program::find($feeAllocation->applicable_id);
        } elseif ($feeAllocation->applicable_to === 'class' && $feeAllocation->applicable_id) {
            $applicableEntity = Classes::find($feeAllocation->applicable_id);
        } elseif ($feeAllocation->applicable_to === 'section' && $feeAllocation->applicable_id) {
            $applicableEntity = Section::find($feeAllocation->applicable_id);
        } elseif ($feeAllocation->applicable_to === 'student' && $feeAllocation->applicable_id) {
            $applicableEntity = Student::find($feeAllocation->applicable_id);
        }
        
        return view('finance.fee-allocations.show', compact('feeAllocation', 'applicableEntity'));
    }

    /**
     * Show the form for editing the specified fee allocation.
     *
     * @param  \App\Models\FeeAllocation  $feeAllocation
     * @return \Illuminate\Http\Response
     */
    public function edit(FeeAllocation $feeAllocation)
    {
        $feeTypes = FeeType::where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $programs = Program::orderBy('name')->get();
        $classes = Classes::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        
        $applicableTypes = [
            'program' => 'Program',
            'class' => 'Class',
            'section' => 'Section',
            'student' => 'Individual Student',
        ];
        
        // Get the current applicable entity
        $applicableEntity = null;
        if ($feeAllocation->applicable_to === 'program' && $feeAllocation->applicable_id) {
            $applicableEntity = Program::find($feeAllocation->applicable_id);
        } elseif ($feeAllocation->applicable_to === 'class' && $feeAllocation->applicable_id) {
            $applicableEntity = Classes::find($feeAllocation->applicable_id);
        } elseif ($feeAllocation->applicable_to === 'section' && $feeAllocation->applicable_id) {
            $applicableEntity = Section::find($feeAllocation->applicable_id);
        } elseif ($feeAllocation->applicable_to === 'student' && $feeAllocation->applicable_id) {
            $applicableEntity = Student::find($feeAllocation->applicable_id);
        }
        
        return view('finance.fee-allocations.edit', compact(
            'feeAllocation',
            'feeTypes',
            'academicYears',
            'programs',
            'classes',
            'sections',
            'applicableTypes',
            'applicableEntity'
        ));
    }

    /**
     * Update the specified fee allocation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeeAllocation  $feeAllocation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeeAllocation $feeAllocation)
    {
        $validated = $request->validate([
            'fee_type_id' => 'required|exists:fee_types,id',
            'applicable_to' => 'required|in:class,program,section,student',
            'academic_year_id' => 'required|exists:academic_years,id',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'academic_term' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        
        // Validate applicable_id based on applicable_to
        if ($request->applicable_to === 'program') {
            $request->validate(['program_id' => 'required|exists:programs,id']);
            $validated['applicable_id'] = $request->program_id;
        } elseif ($request->applicable_to === 'class') {
            $request->validate(['class_id' => 'required|exists:classes,id']);
            $validated['applicable_id'] = $request->class_id;
        } elseif ($request->applicable_to === 'section') {
            $request->validate(['section_id' => 'required|exists:sections,id']);
            $validated['applicable_id'] = $request->section_id;
        } elseif ($request->applicable_to === 'student') {
            $request->validate(['student_id' => 'required|exists:students,id']);
            $validated['applicable_id'] = $request->student_id;
        }
        
        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');
        
        $feeAllocation->update($validated);
        
        return redirect()->route('fee-allocations.index')
            ->with('success', 'Fee allocation updated successfully.');
    }

    /**
     * Remove the specified fee allocation from storage.
     *
     * @param  \App\Models\FeeAllocation  $feeAllocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeeAllocation $feeAllocation)
    {
        // Check if the fee allocation is already being used in invoices
        if ($feeAllocation->invoiceItems()->count() > 0) {
            return redirect()->route('fee-allocations.index')
                ->with('error', 'Cannot delete a fee allocation that has associated invoice items.');
        }
        
        $feeAllocation->delete();
        
        return redirect()->route('fee-allocations.index')
            ->with('success', 'Fee allocation deleted successfully.');
    }
} 
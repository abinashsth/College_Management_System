<?php

namespace App\Http\Controllers;

use App\Models\GradeSystem;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GradeSystemController extends Controller
{
    /**
     * Display a listing of the grade systems.
     */
    public function index()
    {
        $gradeSystems = GradeSystem::with('scales', 'creator', 'updatedBy')->get();
        
        return view('admin.grade-systems.index', compact('gradeSystems'));
    }

    /**
     * Show the form for creating a new grade system.
     */
    public function create()
    {
        return view('admin.grade-systems.create');
    }

    /**
     * Store a newly created grade system in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:grade_systems,name',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'max_gpa' => 'required|numeric|min:0|max:10',
            'pass_percentage' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        
        try {
            $gradeSystem = GradeSystem::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_default' => $request->is_default ?? false,
                'is_active' => $request->is_active ?? true,
                'max_gpa' => $request->max_gpa,
                'pass_percentage' => $request->pass_percentage,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.grade-systems.show', $gradeSystem)
                ->with('success', 'Grade system created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'An error occurred while creating the grade system: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified grade system.
     */
    public function show(GradeSystem $gradeSystem)
    {
        $gradeSystem->load('scales', 'creator', 'updatedBy');
        
        return view('admin.grade-systems.show', compact('gradeSystem'));
    }

    /**
     * Show the form for editing the specified grade system.
     */
    public function edit(GradeSystem $gradeSystem)
    {
        return view('admin.grade-systems.edit', compact('gradeSystem'));
    }

    /**
     * Update the specified grade system in storage.
     */
    public function update(Request $request, GradeSystem $gradeSystem)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('grade_systems')->ignore($gradeSystem)],
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'max_gpa' => 'required|numeric|min:0|max:10',
            'pass_percentage' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        
        try {
            $gradeSystem->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_default' => $request->is_default ?? false,
                'is_active' => $request->is_active ?? true,
                'max_gpa' => $request->max_gpa,
                'pass_percentage' => $request->pass_percentage,
                'updated_by' => Auth::id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.grade-systems.show', $gradeSystem)
                ->with('success', 'Grade system updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'An error occurred while updating the grade system: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified grade system from storage.
     */
    public function destroy(GradeSystem $gradeSystem)
    {
        // Check if it's used by any results
        if ($gradeSystem->results()->exists()) {
            return back()->with('error', 'Cannot delete this grade system as it is associated with one or more results.');
        }
        
        try {
            $gradeSystem->delete();
            
            return redirect()->route('admin.grade-systems.index')
                ->with('success', 'Grade system deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting the grade system: ' . $e->getMessage());
        }
    }

    /**
     * Display scales for a specific grade system.
     */
    public function scales(GradeSystem $gradeSystem)
    {
        $gradeSystem->load('scales');
        
        return view('admin.grade-systems.scales', compact('gradeSystem'));
    }

    /**
     * Show the form for creating a new grade scale.
     */
    public function createScale(GradeSystem $gradeSystem)
    {
        return view('admin.grade-systems.create-scale', compact('gradeSystem'));
    }

    /**
     * Store a newly created grade scale in storage.
     */
    public function storeScale(Request $request, GradeSystem $gradeSystem)
    {
        $request->validate([
            'grade' => ['required', 'string', 'max:10', Rule::unique('grade_scales')->where(function ($query) use ($gradeSystem) {
                return $query->where('grade_system_id', $gradeSystem->id);
            })],
            'name' => 'nullable|string|max:255',
            'min_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($request, $gradeSystem) {
                    // Check if the range overlaps with existing ranges
                    $maxPercentage = $request->max_percentage;
                    $overlapping = $gradeSystem->scales()
                        ->where(function ($query) use ($value, $maxPercentage) {
                            $query->whereBetween('min_percentage', [$value, $maxPercentage])
                                ->orWhereBetween('max_percentage', [$value, $maxPercentage])
                                ->orWhere(function ($q) use ($value, $maxPercentage) {
                                    $q->where('min_percentage', '<=', $value)
                                        ->where('max_percentage', '>=', $maxPercentage);
                                });
                        })->exists();

                    if ($overlapping) {
                        $fail('The percentage range overlaps with an existing grade scale.');
                    }
                },
            ],
            'max_percentage' => 'required|numeric|min:0|max:100|gte:min_percentage',
            'grade_point' => 'required|numeric|min:0|max:' . $gradeSystem->max_gpa,
            'remarks' => 'nullable|string|max:255',
            'is_failing' => 'boolean',
            'color_code' => 'nullable|string|max:7',
        ]);

        try {
            $gradeScale = $gradeSystem->scales()->create([
                'grade' => $request->grade,
                'name' => $request->name,
                'min_percentage' => $request->min_percentage,
                'max_percentage' => $request->max_percentage,
                'grade_point' => $request->grade_point,
                'remarks' => $request->remarks,
                'is_failing' => $request->is_failing ?? false,
                'color_code' => $request->color_code,
            ]);
            
            return redirect()->route('admin.grade-systems.scales', $gradeSystem)
                ->with('success', 'Grade scale created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An error occurred while creating the grade scale: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a grade scale.
     */
    public function editScale(GradeSystem $gradeSystem, GradeScale $scale)
    {
        return view('admin.grade-systems.edit-scale', compact('gradeSystem', 'scale'));
    }

    /**
     * Update the specified grade scale in storage.
     */
    public function updateScale(Request $request, GradeSystem $gradeSystem, GradeScale $scale)
    {
        $request->validate([
            'grade' => ['required', 'string', 'max:10', Rule::unique('grade_scales')->where(function ($query) use ($gradeSystem) {
                return $query->where('grade_system_id', $gradeSystem->id);
            })->ignore($scale)],
            'name' => 'nullable|string|max:255',
            'min_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($request, $gradeSystem, $scale) {
                    // Check if the range overlaps with existing ranges (excluding current scale)
                    $maxPercentage = $request->max_percentage;
                    $overlapping = $gradeSystem->scales()
                        ->where('id', '!=', $scale->id)
                        ->where(function ($query) use ($value, $maxPercentage) {
                            $query->whereBetween('min_percentage', [$value, $maxPercentage])
                                ->orWhereBetween('max_percentage', [$value, $maxPercentage])
                                ->orWhere(function ($q) use ($value, $maxPercentage) {
                                    $q->where('min_percentage', '<=', $value)
                                        ->where('max_percentage', '>=', $maxPercentage);
                                });
                        })->exists();

                    if ($overlapping) {
                        $fail('The percentage range overlaps with an existing grade scale.');
                    }
                },
            ],
            'max_percentage' => 'required|numeric|min:0|max:100|gte:min_percentage',
            'grade_point' => 'required|numeric|min:0|max:' . $gradeSystem->max_gpa,
            'remarks' => 'nullable|string|max:255',
            'is_failing' => 'boolean',
            'color_code' => 'nullable|string|max:7',
        ]);

        try {
            $scale->update([
                'grade' => $request->grade,
                'name' => $request->name,
                'min_percentage' => $request->min_percentage,
                'max_percentage' => $request->max_percentage,
                'grade_point' => $request->grade_point,
                'remarks' => $request->remarks,
                'is_failing' => $request->is_failing ?? false,
                'color_code' => $request->color_code,
            ]);
            
            return redirect()->route('admin.grade-systems.scales', $gradeSystem)
                ->with('success', 'Grade scale updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An error occurred while updating the grade scale: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified grade scale from storage.
     */
    public function destroyScale(GradeSystem $gradeSystem, GradeScale $scale)
    {
        try {
            $scale->delete();
            
            return redirect()->route('admin.grade-systems.scales', $gradeSystem)
                ->with('success', 'Grade scale deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting the grade scale: ' . $e->getMessage());
        }
    }

    /**
     * Set a grade system as the default.
     */
    public function setDefault(GradeSystem $gradeSystem)
    {
        try {
            $gradeSystem->setAsDefault();
            
            return redirect()->route('admin.grade-systems.index')
                ->with('success', 'Default grade system set successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while setting the default grade system: ' . $e->getMessage());
        }
    }
} 
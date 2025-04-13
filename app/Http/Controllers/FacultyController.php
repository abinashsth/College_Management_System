<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\User;
use App\Traits\PreventsDuplicateQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class FacultyController extends Controller
{
    use PreventsDuplicateQueries;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faculties = Faculty::paginate(10);
        return view('faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('faculties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:faculties',
            'slug' => 'nullable|string|max:255|unique:faculties',
            'code' => 'required|string|max:20|unique:faculties',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'established_date' => 'nullable|date',
            'status' => 'nullable|boolean',
        ]);

        // Generate slug if not provided
        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $filename = time() . '_' . $logoFile->getClientOriginalName();
            $logoFile->storeAs('public/faculty_logos', $filename);
            $validated['logo'] = $filename;
        }

        DB::beginTransaction();
        
        try {
            $faculty = Faculty::create($validated);
            
            DB::commit();
            return redirect()->route('faculties.index')
                ->with('success', 'Faculty created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while creating the faculty.'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty)
    {
        $faculty->load(['departments']);
        return view('faculties.show', compact('faculty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faculty $faculty)
    {
        return view('faculties.edit', compact('faculty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('faculties')->ignore($faculty->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('faculties')->ignore($faculty->id)],
            'code' => ['required', 'string', 'max:20', Rule::unique('faculties')->ignore($faculty->id)],
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'established_date' => 'nullable|date',
            'status' => 'nullable|boolean',
        ]);

        // Generate slug if not provided
        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($faculty->logo) {
                Storage::delete('public/faculty_logos/' . $faculty->logo);
            }
            
            $logoFile = $request->file('logo');
            $filename = time() . '_' . $logoFile->getClientOriginalName();
            $logoFile->storeAs('public/faculty_logos', $filename);
            $validated['logo'] = $filename;
        }

        DB::beginTransaction();
        
        try {
            $faculty->update($validated);
            
            DB::commit();
            return redirect()->route('faculties.index')
                ->with('success', 'Faculty updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while updating the faculty.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        // Check if faculty has departments
        if ($faculty->departments()->count() > 0) {
            return back()->withErrors(['message' => 'Cannot delete faculty with associated departments.']);
        }

        // Begin transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // Delete logo if exists
            if ($faculty->logo) {
                Storage::delete('public/faculty_logos/' . $faculty->logo);
            }
            
            $faculty->delete();
            
            DB::commit();
            return redirect()->route('faculties.index')
                ->with('success', 'Faculty deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while deleting the faculty.']);
        }
    }
    
    /**
     * Display faculty dashboard with statistics.
     */
    public function dashboard(Faculty $faculty)
    {
        // Use eager loading for relationships to prevent N+1 queries
        $faculty->load(['departments' => function($query) {
            $query->withCount(['programs', 'teachers', 'students']);
        }]);
        
        // Calculate statistics without triggering additional queries
        $totalDepartments = $faculty->departments->count();
        $totalPrograms = $faculty->departments->sum('programs_count');
        $totalTeachers = $faculty->departments->sum('teachers_count');
        $totalStudents = $faculty->departments->sum('students_count');
        
        $departmentStats = $faculty->departments;
        
        return view('faculties.dashboard', compact(
            'faculty',
            'totalDepartments',
            'totalPrograms',
            'totalTeachers',
            'totalStudents',
            'departmentStats'
        ));
    }
}

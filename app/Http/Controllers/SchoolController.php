<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schools = School::orderBy('id', 'desc')->paginate(10);
        return view('schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('schools.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:schools,email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('school_logos', 'public');
            $data['logo'] = $logoPath;
        }

        // If this is the first school or set as active, deactivate all other schools
        if ($request->has('is_active') && $request->is_active) {
            School::where('is_active', true)->update(['is_active' => false]);
        }

        School::create($data);

        return redirect()->route('schools.index')
            ->with('success', 'School details created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        return view('schools.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:schools,email,' . $school->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            
            $logoPath = $request->file('logo')->store('school_logos', 'public');
            $data['logo'] = $logoPath;
        }

        // If setting this school as active, deactivate all other schools
        if ($request->has('is_active') && $request->is_active && !$school->is_active) {
            School::where('is_active', true)->update(['is_active' => false]);
        }

        $school->update($data);

        return redirect()->route('schools.index')
            ->with('success', 'School details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        // Delete logo if exists
        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }
        
        $school->delete();

        return redirect()->route('schools.index')
            ->with('success', 'School details deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\FacultyDean;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyDeanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deans = FacultyDean::with(['faculty', 'user'])->orderBy('is_active', 'desc')->get();
        return view('faculty-deans.index', compact('deans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $faculties = Faculty::whereDoesntHave('dean', function($query) {
            $query->where('is_active', true);
        })->get();
        
        // Get users with appropriate roles (e.g., teachers, admins)
        $users = User::role(['teacher', 'admin'])->get();
        
        return view('faculty-deans.create', compact('faculties', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:academic_structures,id',
            'user_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'appointment_reference' => 'nullable|string|max:255',
            'job_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Check if this faculty already has an active dean
        $existingDean = FacultyDean::where('faculty_id', $validated['faculty_id'])
            ->where('is_active', true)
            ->first();
            
        if ($existingDean && $validated['is_active']) {
            return back()->with('error', 'This faculty already has an active dean. Please deactivate the current dean first.')
                ->withInput();
        }
        
        // Check if this user is already an active dean somewhere else
        $userIsDeanElsewhere = FacultyDean::where('user_id', $validated['user_id'])
            ->where('is_active', true)
            ->exists();
            
        if ($userIsDeanElsewhere && $validated['is_active']) {
            return back()->with('error', 'This user is already assigned as dean to another faculty.')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            FacultyDean::create($validated);
            
            // Add faculty-related permissions to the user
            $user = User::find($validated['user_id']);
            $faculty = Faculty::find($validated['faculty_id']);
            
            DB::commit();
            
            return redirect()->route('faculty-deans.index')
                ->with('success', 'Faculty dean assigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error assigning dean: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FacultyDean $facultyDean)
    {
        $facultyDean->load(['faculty', 'user']);
        return view('faculty-deans.show', compact('facultyDean'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FacultyDean $facultyDean)
    {
        $facultyDean->load(['faculty', 'user']);
        
        // For faculty selection, include the current faculty even if it has an active dean
        $faculties = Faculty::where(function($query) use ($facultyDean) {
            $query->whereDoesntHave('dean', function($q) {
                $q->where('is_active', true);
            })->orWhere('id', $facultyDean->faculty_id);
        })->get();
        
        // Get users with appropriate roles
        $users = User::role(['teacher', 'admin'])->get();
        
        return view('faculty-deans.edit', compact('facultyDean', 'faculties', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FacultyDean $facultyDean)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:academic_structures,id',
            'user_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'appointment_reference' => 'nullable|string|max:255',
            'job_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // If faculty or user is changed, perform additional validations
        if ($facultyDean->faculty_id != $validated['faculty_id']) {
            // Check if the new faculty already has an active dean
            $existingDean = FacultyDean::where('faculty_id', $validated['faculty_id'])
                ->where('is_active', true)
                ->where('id', '!=', $facultyDean->id)
                ->first();
                
            if ($existingDean && $validated['is_active']) {
                return back()->with('error', 'The selected faculty already has an active dean. Please deactivate the current dean first.')
                    ->withInput();
            }
        }
        
        if ($facultyDean->user_id != $validated['user_id']) {
            // Check if the new user is already an active dean somewhere else
            $userIsDeanElsewhere = FacultyDean::where('user_id', $validated['user_id'])
                ->where('is_active', true)
                ->where('id', '!=', $facultyDean->id)
                ->exists();
                
            if ($userIsDeanElsewhere && $validated['is_active']) {
                return back()->with('error', 'The selected user is already assigned as dean to another faculty.')
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $facultyDean->update($validated);
            
            DB::commit();
            
            return redirect()->route('faculty-deans.index')
                ->with('success', 'Faculty dean updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating dean: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FacultyDean $facultyDean)
    {
        $facultyName = $facultyDean->faculty->name;
        $userName = $facultyDean->user->name;
        
        $facultyDean->delete();
        
        return redirect()->route('faculty-deans.index')
            ->with('success', "Dean assignment for {$userName} to {$facultyName} has been removed.");
    }
}

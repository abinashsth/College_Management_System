<?php

namespace App\Http\Controllers;

use App\Models\DepartmentHead;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departmentHeads = DepartmentHead::with(['department', 'user'])
            ->orderByDesc('is_active')
            ->get();
        return view('department-heads.index', compact('departmentHeads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get departments without active heads
        $departmentsWithoutHeads = Department::whereDoesntHave('head', function ($query) {
                $query->where('is_active', true);
            })
            ->get();
            
        // Get users with role teacher or admin
        $eligibleUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['teacher', 'admin']);
            })
            ->get();
            
        return view('department-heads.create', compact('departmentsWithoutHeads', 'eligibleUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:academic_structures,id',
            'user_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'appointment_reference' => 'nullable|string|max:255',
            'job_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Check if department already has an active head
        $existingHead = DepartmentHead::where('department_id', $validated['department_id'])
            ->where('is_active', true)
            ->first();
            
        if ($existingHead && $validated['is_active']) {
            return back()->withErrors(['department_id' => 'This department already has an active head.'])
                ->withInput();
        }
        
        // Check if user is already an active head of another department
        $existingUserHead = DepartmentHead::where('user_id', $validated['user_id'])
            ->where('is_active', true)
            ->first();
            
        if ($existingUserHead && $validated['is_active']) {
            return back()->withErrors(['user_id' => 'This user is already an active head of another department.'])
                ->withInput();
        }

        // Begin transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // Create the new department head
            $departmentHead = new DepartmentHead($validated);
            $departmentHead->save();
            
            DB::commit();
            return redirect()->route('department-heads.index')
                ->with('success', 'Department head assigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while assigning the department head.'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DepartmentHead $departmentHead)
    {
        $departmentHead->load(['department', 'user']);
        return view('department-heads.show', compact('departmentHead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DepartmentHead $departmentHead)
    {
        // Get all departments
        $departments = Department::all();
        
        // Get users with role teacher or admin
        $eligibleUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['teacher', 'admin']);
            })
            ->get();
            
        return view('department-heads.edit', compact('departmentHead', 'departments', 'eligibleUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DepartmentHead $departmentHead)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:academic_structures,id',
            'user_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'appointment_reference' => 'nullable|string|max:255',
            'job_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // If the department has changed or is_active is now true, check for existing head
        if (($departmentHead->department_id != $validated['department_id'] || 
             (!$departmentHead->is_active && $validated['is_active'])) && 
            $validated['is_active']) {
            
            $existingHead = DepartmentHead::where('department_id', $validated['department_id'])
                ->where('is_active', true)
                ->where('id', '!=', $departmentHead->id)
                ->first();
                
            if ($existingHead) {
                return back()->withErrors(['department_id' => 'This department already has an active head.'])
                    ->withInput();
            }
        }
        
        // If the user has changed or is_active is now true, check if user is already a head
        if (($departmentHead->user_id != $validated['user_id'] || 
             (!$departmentHead->is_active && $validated['is_active'])) && 
            $validated['is_active']) {
            
            $existingUserHead = DepartmentHead::where('user_id', $validated['user_id'])
                ->where('is_active', true)
                ->where('id', '!=', $departmentHead->id)
                ->first();
                
            if ($existingUserHead) {
                return back()->withErrors(['user_id' => 'This user is already an active head of another department.'])
                    ->withInput();
            }
        }

        // Begin transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            $departmentHead->update($validated);
            
            DB::commit();
            return redirect()->route('department-heads.index')
                ->with('success', 'Department head updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'An error occurred while updating the department head.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DepartmentHead $departmentHead)
    {
        $departmentHead->delete();
        
        return redirect()->route('department-heads.index')
            ->with('success', 'Department head removed successfully');
    }
}

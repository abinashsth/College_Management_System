<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Display a listing of the staff members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Staff::with(['user', 'department']);
        
        // Apply filters if provided
        if ($request->has('department')) {
            $query->where('department_id', $request->department);
        }
        
        if ($request->has('position')) {
            $query->where('position', $request->position);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $staff = $query->paginate(15);
        $departments = Department::all();
        
        return view('staff.index', compact('staff', 'departments'));
    }

    /**
     * Show the form for creating a new staff member.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::all();
        $positions = [
            'teacher' => 'Teacher',
            'lecturer' => 'Lecturer',
            'professor' => 'Professor',
            'assistant professor' => 'Assistant Professor',
            'associate professor' => 'Associate Professor',
            'administrator' => 'Administrator',
            'clerk' => 'Clerk',
            'officer' => 'Officer',
            'coordinator' => 'Coordinator'
        ];
        
        $employmentTypes = [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'contract' => 'Contract',
            'visiting' => 'Visiting',
            'temporary' => 'Temporary'
        ];
        
        return view('staff.create', compact('departments', 'positions', 'employmentTypes'));
    }

    /**
     * Store a newly created staff member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'staff_id' => 'required|string|max:50|unique:staff',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string',
            'employment_type' => 'required|string',
            'employment_start_date' => 'required|date',
            'contact_number' => 'required|string',
            'address' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            
            // Assign teacher role
            $role = Role::where('name', 'Teacher')->first();
            if ($role) {
                $user->assignRole($role);
            }
            
            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('staff-photos', 'public');
            }
            
            // Process qualifications and specializations
            $qualifications = $request->qualifications ? explode(',', $request->qualifications) : [];
            $specializations = $request->specializations ? explode(',', $request->specializations) : [];
            
            // Create staff record
            $staff = Staff::create([
                'user_id' => $user->id,
                'staff_id' => $request->staff_id,
                'department_id' => $request->department_id,
                'position' => $request->position,
                'employment_type' => $request->employment_type,
                'employment_start_date' => $request->employment_start_date,
                'employment_end_date' => $request->employment_end_date,
                'qualifications' => $qualifications,
                'specializations' => $specializations,
                'contact_number' => $request->contact_number,
                'emergency_contact' => $request->emergency_contact,
                'address' => $request->address,
                'photo' => $photoPath,
                'status' => 'active',
                'bio' => $request->bio,
            ]);
            
            DB::commit();
            
            return redirect()->route('staff.index')
                ->with('success', 'Staff member created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'Failed to create staff member: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified staff member.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff)
    {
        $staff->load(['user', 'department', 'subjects', 'leaveApplications', 'teachingLoads']);
        
        return view('staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified staff member.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit(Staff $staff)
    {
        $departments = Department::all();
        $positions = [
            'teacher' => 'Teacher',
            'lecturer' => 'Lecturer',
            'professor' => 'Professor',
            'assistant professor' => 'Assistant Professor',
            'associate professor' => 'Associate Professor',
            'administrator' => 'Administrator',
            'clerk' => 'Clerk',
            'officer' => 'Officer',
            'coordinator' => 'Coordinator'
        ];
        
        $employmentTypes = [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'contract' => 'Contract',
            'visiting' => 'Visiting',
            'temporary' => 'Temporary'
        ];
        
        $staff->load('user');
        
        return view('staff.edit', compact('staff', 'departments', 'positions', 'employmentTypes'));
    }

    /**
     * Update the specified staff member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->user_id,
            'staff_id' => 'required|string|max:50|unique:staff,staff_id,' . $staff->id,
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string',
            'employment_type' => 'required|string',
            'employment_start_date' => 'required|date',
            'contact_number' => 'required|string',
            'address' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive,on-leave',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update user
            $staff->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            
            // Update password if provided
            if ($request->filled('password')) {
                $staff->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Remove old photo if exists
                if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                    Storage::disk('public')->delete($staff->photo);
                }
                
                $photoPath = $request->file('photo')->store('staff-photos', 'public');
            } else {
                $photoPath = $staff->photo;
            }
            
            // Process qualifications and specializations
            $qualifications = $request->qualifications ? explode(',', $request->qualifications) : [];
            $specializations = $request->specializations ? explode(',', $request->specializations) : [];
            
            // Update staff record
            $staff->update([
                'staff_id' => $request->staff_id,
                'department_id' => $request->department_id,
                'position' => $request->position,
                'employment_type' => $request->employment_type,
                'employment_start_date' => $request->employment_start_date,
                'employment_end_date' => $request->employment_end_date,
                'qualifications' => $qualifications,
                'specializations' => $specializations,
                'contact_number' => $request->contact_number,
                'emergency_contact' => $request->emergency_contact,
                'address' => $request->address,
                'photo' => $photoPath,
                'status' => $request->status,
                'bio' => $request->bio,
            ]);
            
            DB::commit();
            
            return redirect()->route('staff.show', $staff)
                ->with('success', 'Staff member updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'Failed to update staff member: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified staff member from storage.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff)
    {
        try {
            // Delete photo if exists
            if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                Storage::disk('public')->delete($staff->photo);
            }
            
            // Delete the staff record
            $staff->delete();
            
            // Note: We're not deleting the user record to maintain data integrity
            // The user will be marked as inactive or can be handled separately
            
            return redirect()->route('staff.index')
                ->with('success', 'Staff member deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete staff member: ' . $e->getMessage());
        }
    }
} 
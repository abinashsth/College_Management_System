<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        return view('employee.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_code' => 'required|unique:employees',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees',
                'department' => 'required|string',
                'salary' => 'required|numeric|min:0',
                'position' => 'required|string',
                'status' => 'required|string', // Changed from is_active to match update method
                'join_date' => 'required|date'
            ]);

            Employee::create($validated);

            return redirect()->route('employee.index')
                ->with('success', 'Employee created successfully.');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->with('error', 'An error occurred while creating the employee.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('employee.show', compact('employee'));  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $departments = Department::all(); // ✅ Fetch departments from the database
     
        return view('employee.edit', compact('employee', 'departments'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'employee_code' => 'required|unique:employees,employee_code,'.$employee->id,
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email,'.$employee->id,
                'department' => 'required|string',
                'salary' => 'required|numeric|min:0',
                'position' => 'required|string',
                'status' => 'required|in:active,inactive', // Add validation for status values
                'join_date' => 'required|date|before_or_equal:today' // Ensure join date is not in future
            ]);

            // Update employee record with validated data
            $employee->update($validated);

            // Log the update action
            \Log::info('Employee updated: ' . $employee->id);

            return redirect()->route('employee.index')
                ->with('success', 'Employee updated successfully.');
        } catch (ValidationException $e) {
            \Log::error('Validation error while updating employee: ' . $e->getMessage());
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating employee: ' . $e->getMessage());
            return back()
                ->with('error', 'An error occurred while updating the employee.')
                ->withInput();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            return redirect()->route('employee.index')
                ->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting the employee.');
        }
    }
}

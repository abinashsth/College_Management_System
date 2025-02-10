<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::paginate(10);
        return view('account.employee.index', compact('employees'));
    }

    public function create()
    {
        // Just return the view for creating a new employee
        return view('account.employee.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'employee_id' => 'required|numeric',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employee',
            'department' => 'required|string|max:255',
            'designation' => 'required|string|max:255', 
            'contact' => 'required|numeric',
            'status' => 'required|string|max:255',
        ]);
        
        Employee::create([
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
            'designation' => $request->designation,
            'contact' => $request->contact,
            'status' => $request->status,
        ]);

        return redirect()->route('account.employee.index')->with('success', 'Employee created successfully.');
    }

    public function show($id)
    {
        $employee = Employee::with('salaryIncrements')->findOrFail($id);
        return response()->json($employee);
    }
    

    public function edit($id)
    {
        // Find the employee record
        $employee = Employee::findOrFail($id);
    
        // Pass the employee data to the edit view
        return view('account.employee.edit', compact('employee'));
    }
    
    public function update(Request $request, $id)
    {
        // Find the employee record by ID
        $employee = Employee::findOrFail($id);

        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
           'email' => 'required|email|unique:employees,email,' . $employee->id,
            'department' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'contact' => 'required|numeric',
        ]);
// Update the employee record
        $employee->update($validated);

        return redirect()->route('account.employee.update')->with('success', 'Employee updated successfully.');
    }

    public function destroy($id)
    {
        // Find the employee by ID
        $employee = Employee::findOrFail($id);
    
        // Delete the employee record
        $employee->delete();
    
        // Redirect with a success message
        return redirect()->route('account.employee.index')->with('success', 'Employee deleted successfully.');
    }
    
}

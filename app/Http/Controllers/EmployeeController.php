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
        $employees = Employee::latest()->paginate(10);
        return view('account.employee.index', compact('employees'));
    }

    public function create()
    {
        return view('account.employee.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|numeric|unique:employee,employee_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employee',
            'department' => 'required|string|max:255',
            'designation' => 'required|string|max:255', 
            'contact' => 'required|string|max:20',
            'status' => 'required|boolean',
        ]);
        
        $employee = Employee::create($validated);

        return redirect()
            ->route('account.employee.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        return response()->json(
            $employee->load('salaryIncrements')
        );
    }

    public function edit(Employee $employee)
    {
        return view('account.employee.edit', compact('employee'));
    }
    
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employee,email,' . $employee->id,
            'department' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'status' => 'required|boolean'
        ]);

        $employee->update($validated);

        return redirect()
            ->route('account.employee.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
    
        return redirect()
            ->route('account.employee.index')
            ->with('success', 'Employee deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('department');
        
        // Filter by department if provided
        if ($request->filled('department_id') && $request->department_id != 'all') {
            $query->where('department_id', $request->department_id);
        }
        
        // Search by employee name or ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        
        $departments = Department::all(); // Fetch all departments
        $employees = $query->paginate(10); // Use pagination
    
        return view('account.employees.index', compact('departments', 'employees'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('account.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|unique:employees,employee_id',
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:255',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'join_date' => 'nullable|date',
        ]);
        
        Employee::create($request->all());
        
        return redirect()->route('account.employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load('department');
        return view('account.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::orderBy('name')->get();
        return view('account.employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,' . $employee->id,
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:255',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'join_date' => 'nullable|date',
        ]);
        
        $employee->update($request->all());
        
        return redirect()->route('account.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('account.employees.index')->with('success', 'Employee deleted successfully.');
    }
}
